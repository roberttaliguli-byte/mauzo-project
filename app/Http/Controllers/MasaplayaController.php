<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Masaplaya;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class MasaplayaController extends Controller
{
    /**
     * Get the authenticated user from either guard
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        abort(403, 'Unauthorized - Please login first');
    }
    
    /**
     * Get company for current user
     */
    private function getCompany()
    {
        $user = $this->getAuthenticatedUser();
        return $user->company;
    }
    
    /**
     * Get user name for history
     */
    private function getUserName()
    {
        $user = $this->getAuthenticatedUser();
        return $user->name ?? $user->jina ?? 'Mfanyakazi';
    }
    
    /**
     * Display list of all masaplaya (suppliers)
     */
    public function index()
    {
        $company = $this->getCompany();

        $masaplaya = Masaplaya::where('company_id', $company->id)
            ->latest()
            ->get();

        return view('masaplaya.index', compact('masaplaya'));
    }

    /**
     * Store a new masaplaya
     */
    public function store(Request $request)
    {
        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'nullable|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anaopoishi' => 'nullable|string|max:255',
            'ofisi' => 'nullable|string|max:255',
            'maelezo' => 'nullable|string',
        ]);

        $company = $this->getCompany();

        $masaplaya = $company->masaplaya()->create($request->all());

        History::create([
            'user'    => $this->getUserName(),
            'action'  => 'Ameongeza Masaplaya',
            'details' => "Masaplaya mpya: {$masaplaya->jina}, Simu: {$masaplaya->simu}",
        ]);

        return redirect()->route('masaplaya.index')->with('success', 'Masaplaya ameongezwa kikamilifu!');
    }

    /**
     * Update an existing masaplaya
     */
    public function update(Request $request, Masaplaya $masaplaya)
    {
        $companyId = $this->getCompany()->id;
        
        if ($masaplaya->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kubadilisha msambazaji huyu.');
        }

        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'nullable|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anaopoishi' => 'nullable|string|max:255',
            'ofisi' => 'nullable|string|max:255',
            'maelezo' => 'nullable|string',
        ]);

        $masaplaya->update($request->all());

        History::create([
            'user'    => $this->getUserName(),
            'action'  => 'Amebadilisha Masaplaya',
            'details' => "Masaplaya: {$masaplaya->jina} amebadilishwa.",
        ]);

        return redirect()->route('masaplaya.index')->with('success', 'Taarifa zimebadilishwa kikamilifu!');
    }

    /**
     * Delete masaplaya
     */
    public function destroy(Masaplaya $masaplaya)
    {
        $companyId = $this->getCompany()->id;
        
        if ($masaplaya->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kufuta msambazaji huyu.');
        }

        $name = $masaplaya->jina;
        $masaplaya->delete();

        History::create([
            'user'    => $this->getUserName(),
            'action'  => 'Amefuta Masaplaya',
            'details' => "Masaplaya: {$name}",
        ]);

        return redirect()->route('masaplaya.index')->with('success', 'Masaplaya amefutwa kikamilifu!');
    }
}