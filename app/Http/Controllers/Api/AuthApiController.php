<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wafanyakazi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * POST /api/login
     * Body: { username, password, guard? }
     *   guard: 'auto' (default) | 'web' | 'mfanyakazi'
     *
     * 'auto' mirrors the web loginPost(): it tries a boss/admin account
     * first, then falls back to an employee (mfanyakazi) account. This lets
     * wafanyakazi use the same single login form as on the website.
     *
     * Returns: { token, guard, user: {...} }
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'guard'    => 'nullable|in:auto,web,mfanyakazi',
        ]);

        $guard = $data['guard'] ?? 'auto';

        if ($guard === 'mfanyakazi') {
            return $this->loginEmployee($data);
        }
        if ($guard === 'web') {
            return $this->loginUser($data);
        }

        // Auto-detect: a matching web account wins (so its approval checks
        // still apply), otherwise try the employee guard with the same
        // credentials — exactly like the web login does.
        $webUser = User::where('username', $data['username'])->first();
        if ($webUser && Hash::check($data['password'], $webUser->password)) {
            return $this->loginUser($data);
        }

        return $this->loginEmployee($data);
    }

    private function loginUser(array $data)
    {
        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Taarifa za kuingia si sahihi.'],
            ]);
        }

        // Mirror web loginPost validation
        if ($user->role === 'admin') {
            if (!$user->is_approved) {
                throw ValidationException::withMessages([
                    'username' => ['Akaunti haijaidhinishwa.'],
                ]);
            }
        } else { // boss
            if ($user->role !== 'boss') {
                throw ValidationException::withMessages([
                    'username' => ['Huna ruhusa ya kuingia hapa.'],
                ]);
            }
            if (!$user->company_id || !$user->company) {
                throw ValidationException::withMessages([
                    'username' => ['Akaunti yako haijaunganishwa na kampuni.'],
                ]);
            }
        }

        $user->forceFill([
            'last_login_at'   => now(),
            'last_activity_at'=> now(),
            'login_count'     => ($user->login_count ?? 0) + 1,
        ])->save();

        $token = $user->createToken('mobile:' . $user->id)->plainTextToken;

        return response()->json([
            'token' => $token,
            'guard' => 'web',
            'user'  => [
                'id'         => $user->id,
                'username'   => $user->username,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'company_id' => $user->company_id,
                'company'    => $user->company ? [
                    'id'           => $user->company->id,
                    'company_name' => $user->company->company_name ?? null,
                    'package'      => $user->company->package ?? null,
                    'package_end'  => $user->company->package_end ?? null,
                ] : null,
            ],
        ]);
    }

    private function loginEmployee(array $data)
    {
        $emp = Wafanyakazi::where('username', $data['username'])->first();

        if (!$emp || !Hash::check($data['password'], $emp->password)) {
            throw ValidationException::withMessages([
                'username' => ['Taarifa za kuingia si sahihi.'],
            ]);
        }

        // Mirror the web login: the `getini` flag decides whether an employee
        // may sign in ('ingia' = allowed, 'simama' = stopped).
        if ($emp->getini !== 'ingia') {
            throw ValidationException::withMessages([
                'username' => ['Hauruhusiwi kuingia kwa sasa.'],
            ]);
        }

        if (!$emp->company_id) {
            throw ValidationException::withMessages([
                'username' => ['Hujaunganishwa na kampuni.'],
            ]);
        }

        // Mirror the web login's package-expiry check for employees.
        $company = $emp->company;
        if (!$company
            || $company->package_end === null
            || \Carbon\Carbon::parse($company->package_end)->isPast()) {
            throw ValidationException::withMessages([
                'username' => [
                    'Samahani, kifurushi cha kampuni kimeisha muda. Wasiliana na mwajiri wako.',
                ],
            ]);
        }

        $emp->forceFill([
            'last_login_at' => now(),
            'login_count'   => ($emp->login_count ?? 0) + 1,
        ])->save();

        $token = $emp->createToken('mobile-emp:' . $emp->id)->plainTextToken;

        return response()->json([
            'token' => $token,
            'guard' => 'mfanyakazi',
            'user'  => [
                'id'         => $emp->id,
                'username'   => $emp->username,
                'name'       => $emp->jina,
                'email'      => $emp->barua_pepe,
                'phone'      => $emp->simu,
                'role'       => $emp->role ?? 'mfanyakazi',
                'uwezo'      => $emp->uwezo ?? 'mdogo',
                'company_id' => $emp->company_id,
                'company'    => [
                    'id'           => $company->id,
                    'company_name' => $company->company_name ?? null,
                    'package'      => $company->package ?? null,
                    'package_end'  => $company->package_end ?? null,
                ],
            ],
        ]);
    }

    /**
     * POST /api/logout  (auth:sanctum)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true]);
    }

    /**
     * GET /api/me  (auth:sanctum)
     */
    public function me(Request $request)
    {
        $u = $request->user();

        // Determine guard from token name
        $token = $request->user()->currentAccessToken();
        $guard = str_starts_with($token->name, 'mobile-emp:') ? 'mfanyakazi' : 'web';

        $payload = [
            'id'         => $u->id,
            'username'   => $u->username,
            'role'       => $u->role,
            'company_id' => $u->company_id,
            'guard'      => $guard,
        ];

        if ($guard === 'mfanyakazi') {
            $payload['name']  = $u->jina;
            $payload['email'] = $u->barua_pepe;
            $payload['phone'] = $u->simu;
            $payload['uwezo'] = $u->uwezo;
            $payload['company'] = $u->company ? [
                'id'           => $u->company->id,
                'company_name' => $u->company->company_name ?? null,
                'package'      => $u->company->package ?? null,
                'package_end'  => $u->company->package_end ?? null,
            ] : null;
        } else {
            $payload['name']    = $u->name;
            $payload['email']   = $u->email;
            $payload['company'] = $u->company ? [
                'id'           => $u->company->id,
                'company_name' => $u->company->company_name ?? null,
                'package'      => $u->company->package ?? null,
                'package_end'  => $u->company->package_end ?? null,
            ] : null;
        }

        return response()->json(['user' => $payload]);
    }
}