<?php

namespace App\Http\Controllers;

use App\Models\Wafanyakazi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class WafanyakaziController extends Controller
{
    /**
     * Display a listing of the employees for the logged-in company.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $perPage = $request->input('per_page', 10);

        $query = Wafanyakazi::where('company_id', $companyId);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('simu', 'LIKE', "%{$search}%")
                  ->orWhere('barua_pepe', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        $wafanyakazi = $query->latest()
                            ->paginate($perPage)
                            ->appends($request->except('page'));

        // Get statistics
        $totalEmployees = $wafanyakazi->total();
        $activeEmployees = $wafanyakazi->where('getini', 'ingia')->count();
        $maleEmployees = $wafanyakazi->where('jinsia', 'Mwanaume')->count();
        $femaleEmployees = $wafanyakazi->where('jinsia', 'Mwanamke')->count();

        // PDF Export
        if ($request->has('export') && $request->export === 'pdf') {
            $data = [
                'wafanyakazi' => Wafanyakazi::where('company_id', $companyId)->latest()->get(),
                'title' => 'Orodha ya Wafanyakazi',
                'date' => now()->format('d/m/Y'),
                'company' => Auth::user()->company->name ?? 'Kampuni',
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
            ];
            
            $pdf = Pdf::loadView('wafanyakazi.pdf', $data);
            return $pdf->download('wafanyakazi-' . date('Y-m-d') . '.pdf');
        }

        return view('wafanyakazi.index', compact(
            'wafanyakazi', 
            'totalEmployees', 
            'activeEmployees', 
            'maleEmployees', 
            'femaleEmployees'
        ));
    }

    /**
     * Store a newly created employee belonging to the logged-in company.
     */
public function store(Request $request)
{
    $request->validate([
        'jina' => 'required|string|max:255',
        'simu' => 'nullable|string|max:20',
        'jinsia' => 'required|string',
        'anuani' => 'nullable|string|max:500',
        'barua_pepe' => 'nullable|email|max:255',
        'ndugu' => 'nullable|string|max:255',
        'simu_ndugu' => 'nullable|string|max:20',
        'username' => [
            'required',
            'string',
            'max:255',
            // Check uniqueness across both users and wafanyakazis tables
            function ($attribute, $value, $fail) {
                // Check if username exists in users table
                $existsInUsers = \App\Models\User::where('username', $value)->exists();
                
                // Check if username exists in wafanyakazis table
                $existsInWafanyakazi = \App\Models\Wafanyakazi::where('username', $value)->exists();
                
                if ($existsInUsers || $existsInWafanyakazi) {
                    $fail('Jina la mtumiaji "' . $value . '" tayari limetumika. Tafadhali chagua jingine.');
                }
            }
        ],
        'password' => 'required|string|min:6|max:255',
        'tarehe_kuzaliwa' => 'nullable|date',
    ], [
        'username.required' => 'Jina la mtumiaji linahitajika.',
        'password.required' => 'Neno la siri linahitajika.',
        'password.min' => 'Neno la siri lazima liwe na angalau herufi 6.',
    ]);

    $data = [
        'jina' => $request->jina,
        'simu' => $request->simu,
        'jinsia' => $request->jinsia,
        'anuani' => $request->anuani,
        'barua_pepe' => $request->barua_pepe,
        'ndugu' => $request->ndugu,
        'simu_ndugu' => $request->simu_ndugu,
        'username' => $request->username,
        'password' => bcrypt($request->password),
        'tarehe_kuzaliwa' => $request->tarehe_kuzaliwa,          
        'company_id' => Auth::guard('web')->user()->company_id,
        'role' => 'mfanyakazi', // ✅ Set role explicitly
        'getini' => $request->getini ?? 'simama',
    ];

    Wafanyakazi::create($data);

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Mfanyakazi amesajiliwa kikamilifu!'
        ]);
    }

    return redirect()->route('wafanyakazi.index')
        ->with('success', 'Mfanyakazi amesajiliwa kikamilifu!');
}

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        $companyId = Auth::user()->company_id;
        $mfanyakazi = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'jina' => 'required|string|max:255',
            'simu' => 'nullable|string|max:20',
            'jinsia' => 'required|string',
            'anuani' => 'nullable|string|max:500',
            'barua_pepe' => 'nullable|email|max:255',
            'ndugu' => 'nullable|string|max:255',
            'simu_ndugu' => 'nullable|string|max:20',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'tarehe_kuzaliwa' => 'nullable|date',
            'getini' => 'in:simama,ingia',
        ]);

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $mfanyakazi->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Taarifa za mfanyakazi zimesasishwa!'
            ]);
        }

        return redirect()->route('wafanyakazi.index')
            ->with('success', 'Taarifa za mfanyakazi zimesasishwa!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id, Request $request)
    {
        $companyId = Auth::user()->company_id;
        $mfanyakazi = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);

        $mfanyakazi->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mfanyakazi amefutwa kikamilifu!'
            ]);
        }

        return redirect()->route('wafanyakazi.index')
            ->with('success', 'Mfanyakazi amefutwa kikamilifu!');
    }
}