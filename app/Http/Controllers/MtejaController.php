<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mteja;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class MtejaController extends Controller
{
    /**
     * Get company ID for current user (works for both guards)
     */
    private function getCompanyId()
    {
        // Check mfanyakazi guard first
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user()->company_id;
        }
        
        // Then check web guard for boss/admin
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->company_id;
        }
        
        // If neither guard is authenticated
        abort(403, 'Unauthorized - Please login first');
    }
    
    /**
     * Get current authenticated user from any guard
     */
    private function getAuthUser()
    {
        if (Auth::guard('mfanyakazi')->check()) {
            return Auth::guard('mfanyakazi')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        return null;
    }
    
    /**
     * Get company for current user
     */
    private function getCompany()
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            abort(403, 'Unauthorized - Please login first');
        }
        
        return $user->company;
    }

    /**
     * Display all customers belonging to the logged-in user's company.
     */
    public function index()
    {
        $company = $this->getCompany();

        // Fetch only customers for the logged-in user's company
        $wateja = Mteja::where('company_id', $company->id)->get();

        // Add statistics for the dashboard
        $stats = [
            'total_customers' => $wateja->count(),
            'new_customers_this_month' => $wateja->where('created_at', '>=', now()->startOfMonth())->count(),
            'total_debts' => 'TZS 0', // You can add debt calculation later
            'vip_customers' => 0, // You can add VIP logic later
        ];

        return view('wateja.index', compact('wateja', 'stats'));
    }

    /**
     * Store a new customer and record history.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anapoishi' => 'nullable|string|max:255',
            'maelezo' => 'nullable|string',
        ]);

        $company = $this->getCompany();
        $user = $this->getAuthUser();

        // ✅ Attach company_id automatically
        $mteja = $company->wateja()->create([
            'jina' => $request->jina,
            'simu' => $request->simu,
            'barua_pepe' => $request->barua_pepe,
            'anapoishi' => $request->anapoishi,
            'maelezo' => $request->maelezo,
        ]);

        // 🧾 Record history
        History::create([
            'user'    => $user->name,
            'action'  => 'Aliongeza Mteja',
            'details' => "Mteja mpya: {$mteja->jina}, Simu: {$mteja->simu}",
        ]);

        return redirect()->route('wateja.index')->with('success', 'Mteja ameongezwa kikamilifu!');
    }

    /**
     * Update customer details and record changes in history.
     */
    public function update(Request $request, Mteja $mteja)
    {
        $companyId = $this->getCompanyId();
        $user = $this->getAuthUser();

        // Ensure this record belongs to the same company
        if ($mteja->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kubadilisha mteja huyu.');
        }

        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'required|string|max:20',
            'barua_pepe' => 'nullable|email|max:255',
            'anapoishi' => 'nullable|string|max:255',
            'maelezo' => 'nullable|string',
        ]);

        $oldData = $mteja->getOriginal();

        $mteja->update($request->only('jina', 'simu', 'barua_pepe', 'anapoishi', 'maelezo'));

        // Detect changes
        $changes = [];
        foreach (['jina', 'simu', 'barua_pepe', 'anapoishi', 'maelezo'] as $field) {
            if ($oldData[$field] != $mteja->$field) {
                $changes[] = ucfirst($field) . ": '{$oldData[$field]}' → '{$mteja->$field}'";
            }
        }

        $changeDetails = empty($changes) ? 'Hakuna mabadiliko makubwa.' : implode(', ', $changes);

        // 🧾 Record history
        History::create([
            'user'    => $user->name,
            'action'  => 'Amebadilisha Mteja',
            'details' => "Mteja: {$mteja->jina} - {$changeDetails}",
        ]);

        return redirect()->route('wateja.index')->with('success', 'Taarifa za mteja zimebadilishwa kikamilifu!');
    }

    /**
     * Delete a customer and record in history.
     */
    public function destroy(Mteja $mteja)
    {
        $companyId = $this->getCompanyId();
        $user = $this->getAuthUser();

        if ($mteja->company_id !== $companyId) {
            abort(403, 'Huna ruhusa ya kufuta mteja huyu.');
        }

        $name = $mteja->jina;
        $simu = $mteja->simu;

        $mteja->delete();

        // 🧾 Record history
        History::create([
            'user'    => $user->name,
            'action'  => 'Amefuta Mteja',
            'details' => "Mteja: {$name}, Simu: {$simu}",
        ]);

        return redirect()->route('wateja.index')->with('success', 'Mteja amefutwa kikamilifu!');
    }

    /**
     * Search customers (for AJAX requests)
     */
    public function search(Request $request)
    {
        $companyId = $this->getCompanyId();
        $query = $request->input('query');

        $wateja = Mteja::where('company_id', $companyId)
            ->where(function($q) use ($query) {
                $q->where('jina', 'LIKE', "%{$query}%")
                  ->orWhere('simu', 'LIKE', "%{$query}%")
                  ->orWhere('barua_pepe', 'LIKE', "%{$query}%")
                  ->orWhere('anapoishi', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json([
            'success' => true,
            'wateja' => $wateja,
            'count' => $wateja->count(),
        ]);
    }
}