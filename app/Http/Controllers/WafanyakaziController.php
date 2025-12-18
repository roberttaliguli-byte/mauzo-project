<?php

namespace App\Http\Controllers;

use App\Models\Wafanyakazi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WafanyakaziController extends Controller
{
    /**
     * Display a listing of the employees for the logged-in company.
     */
    public function index()
    {
    
        $companyId = Auth::user()->company_id;


        $wafanyakazi = Wafanyakazi::where('company_id', $companyId)
            ->latest()
            ->get();

        return view('wafanyakazi.index', compact('wafanyakazi'));
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
            'anuani' => 'nullable|string|max:255',
            'barua_pepe' => 'nullable|email|max:255',
            'ndugu' => 'nullable|string|max:255',
            'simu_ndugu' => 'nullable|string|max:20',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'tarehe_kuzaliwa' => 'nullable|date',
        ]);

        Wafanyakazi::create([
            'jina' => $request->jina,
            'simu' => $request->simu,
            'jinsia' => $request->jinsia,
            'anuani' => $request->anuani,
            'barua_pepe' => $request->barua_pepe,
            'ndugu' => $request->ndugu,
            'simu_ndugu' => $request->simu_ndugu,
            'username' => $request->username,
            'password' => $request->password ? bcrypt($request->password) : null,
            'tarehe_kuzaliwa' => $request->tarehe_kuzaliwa,          
            'company_id' => Auth::user()->company_id, // ✅ Link to company
            'getini' => 'simama', // ✅ Default value for getini
        ]);

        return redirect()->route('wafanyakazi.index')->with('success', 'Mfanyakazi amesajiliwa kikamilifu!');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        $companyId = Auth::user()->company_id;
        $mfanyakazi = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);

        return view('wafanyakazi.edit', compact('mfanyakazi'));
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
            'anuani' => 'nullable|string|max:255',
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

        return redirect()->route('wafanyakazi.index')->with('success', 'Taarifa za mfanyakazi zimesasishwa!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
    {
        $companyId = Auth::user()->company_id;
        $mfanyakazi = Wafanyakazi::where('company_id', $companyId)->findOrFail($id);

        $mfanyakazi->delete();

        return redirect()->route('wafanyakazi.index')->with('success', 'Mfanyakazi amefutwa kikamilifu!');
    }
}
