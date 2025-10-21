<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Masaplaya;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class MasaplayaController extends Controller
{
    /**
     * Orodha ya wasambazaji wa kampuni ya mtumiaji aliyeingia
     */
    public function index()
    {
        $company = Auth::user()->company;

        // Fetch only suppliers belonging to this company
        $masaplaya = Masaplaya::where('company_id', $company->id)->latest()->get();

        return view('masaplaya.index', compact('masaplaya'));
    }

    /**
     * Hifadhi masaplaya mpya
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

        $company = Auth::user()->company;

        // ✅ Create supplier linked to the logged-in user’s company
        $masaplaya = $company->masaplaya()->create([
            'jina' => $request->jina,
            'simu' => $request->simu,
            'barua_pepe' => $request->barua_pepe,
            'anaopoishi' => $request->anaopoishi,
            'ofisi' => $request->ofisi,
            'maelezo' => $request->maelezo,
        ]);

        // 🧾 Record to history
        History::create([
            'user'    => Auth::user()->name,
            'action'  => 'Ameongeza Masaplaya',
            'details' => "Masaplaya mpya: {$masaplaya->jina}, Simu: {$masaplaya->simu}",
        ]);

        return redirect()->route('masaplaya.index')->with('success', 'Masaplaya ameongezwa kikamilifu!');
    }

    /**
     * Badilisha taarifa za masaplaya
     */
    public function update(Request $request, Masaplaya $masaplaya)
    {
        // Ensure this record belongs to the user's company
        if ($masaplaya->company_id !== Auth::user()->company_id) {
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

        $oldData = $masaplaya->getOriginal();

        $masaplaya->update($request->only('jina', 'simu', 'barua_pepe', 'anaopoishi', 'ofisi', 'maelezo'));

        // Detect changes
        $changes = [];
        foreach (['jina', 'simu', 'barua_pepe', 'anaopoishi', 'ofisi', 'maelezo'] as $field) {
            if ($oldData[$field] != $masaplaya->$field) {
                $changes[] = ucfirst($field) . ": '{$oldData[$field]}' → '{$masaplaya->$field}'";
            }
        }

        $changeDetails = empty($changes) ? 'Hakuna mabadiliko makubwa.' : implode(', ', $changes);

        // 🧾 Record to history
        History::create([
            'user'    => Auth::user()->name,
            'action'  => 'Amebadilisha Masaplaya',
            'details' => "Masaplaya: {$masaplaya->jina} - {$changeDetails}",
        ]);

        return redirect()->route('masaplaya.index')->with('success', 'Taarifa za masaplaya zimebadilishwa kikamilifu!');
    }

    /**
     * Futa masaplaya
     */
    public function destroy(Masaplaya $masaplaya)
    {
        // Ensure record belongs to user’s company
        if ($masaplaya->company_id !== Auth::user()->company_id) {
            abort(403, 'Huna ruhusa ya kufuta msambazaji huyu.');
        }

        $name = $masaplaya->jina;
        $simu = $masaplaya->simu;

        $masaplaya->delete();

        // 🧾 Record to history
        History::create([
            'user'    => Auth::user()->name,
            'action'  => 'Amefuta Masaplaya',
            'details' => "Masaplaya: {$name}, Simu: {$simu}",
        ]);

        return redirect()->route('masaplaya.index')->with('success', 'Masaplaya amefutwa kikamilifu!');
    }
}
