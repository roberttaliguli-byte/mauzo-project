<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matumizi;
use App\Models\AinaYaMatumizi; // Add this model
use Illuminate\Support\Facades\Auth;
use App\Models\History;

class MatumiziController extends Controller
{
    /**
     * Onyesha matumizi yote ya kampuni ya mtumiaji
     */
    public function index()
    {
        $company = Auth::user()->company;

        $matumizi = Matumizi::where('company_id', $company->id)
            ->latest()
            ->get();

        // Get all registered expense types for this company
        $aina_za_matumizi = AinaYaMatumizi::where('company_id', $company->id)
            ->withCount(['matumizi' => function($query) use ($company) {
                $query->where('company_id', $company->id);
            }])
            ->latest()
            ->get();

        return view('matumizi.index', compact('matumizi', 'aina_za_matumizi'));
    }

    /**
     * Hifadhi matumizi mapya
     */
    public function store(Request $request)
    {
        $request->validate([
            'gharama' => 'required|numeric|min:0',
            'maelezo' => 'nullable|string',
            'aina' => 'nullable|string|max:255',
            'aina_mpya' => 'nullable|string|max:255',
            'tarehe' => 'nullable|date',
        ]);

        $company = Auth::user()->company;

        // Kama mtumiaji ameandika aina mpya, itumie badala ya ile ya kuchagua
        $aina = $request->filled('aina_mpya')
            ? $request->input('aina_mpya')
            : $request->input('aina');

        $matumizi = $company->matumizi()->create([
            'aina' => $aina,
            'maelezo' => $request->maelezo,
            'gharama' => $request->gharama,
            'created_at' => $request->tarehe ?: now(),
        ]);

        // 🧾 Hifadhi historia
        History::create([
            'user' => Auth::user()->name,
            'action' => 'Ameongeza Matumizi',
            'details' => "Aina: {$aina}, Gharama: {$request->gharama} TZS",
        ]);

        return redirect()->route('matumizi.index')->with('success', 'Matumizi yamehifadhiwa kwa mafanikio!');
    }

    /**
     * Sajili aina mpya ya matumizi
     */
    public function sajiliAina(Request $request)
    {
        $request->validate([
            'jina' => 'required|string|max:255',
            'maelezo' => 'nullable|string',
            'rangi' => 'nullable|string',
            'kategoria' => 'nullable|string'
        ]);

        $company = Auth::user()->company;

        // Check if expense type already exists for this company
        $existingAina = AinaYaMatumizi::where('company_id', $company->id)
            ->where('jina', $request->jina)
            ->first();

        if ($existingAina) {
            return redirect()->back()->with('error', 'Aina ya matumizi tayari imesajiliwa!');
        }

        // Create new expense type
        AinaYaMatumizi::create([
            'jina' => $request->jina,
            'maelezo' => $request->maelezo,
            'rangi' => $request->rangi,
            'kategoria' => $request->kategoria,
            'company_id' => $company->id,
        ]);

        // 🧾 Hifadhi historia
        History::create([
            'user' => Auth::user()->name,
            'action' => 'Amesajili Aina Mpya ya Matumizi',
            'details' => "Aina: {$request->jina}, Kategoria: {$request->kategoria}",
        ]);

        return redirect()->route('matumizi.index')->with('success', 'Aina mpya ya matumizi imesajiliwa kikamilifu!');
    }

    /**
     * Badilisha matumizi
     */
    public function update(Request $request, $id)
    {
        $matumizi = Matumizi::findOrFail($id);

        if ($matumizi->company_id !== Auth::user()->company_id) {
            abort(403, 'Huna ruhusa ya kubadilisha matumizi haya.');
        }

        $request->validate([
            'aina' => 'required|string|max:255',
            'maelezo' => 'nullable|string',
            'gharama' => 'required|numeric|min:0',
        ]);

        $old = $matumizi->getOriginal();

        $matumizi->update($request->only('aina', 'maelezo', 'gharama'));

        // 🧾 Rekodi mabadiliko
        History::create([
            'user' => Auth::user()->name,
            'action' => 'Amebadilisha Matumizi',
            'details' => "Kutoka: Gharama {$old['gharama']} hadi {$matumizi->gharama}",
        ]);

        return redirect()->route('matumizi.index')->with('success', 'Matumizi yamerekebishwa.');
    }

    /**
     * Futa matumizi
     */
    public function destroy($id)
    {
        $matumizi = Matumizi::findOrFail($id);

        if ($matumizi->company_id !== Auth::user()->company_id) {
            abort(403, 'Huna ruhusa ya kufuta matumizi haya.');
        }

        $aina = $matumizi->aina;
        $gharama = $matumizi->gharama;

        $matumizi->delete();

        // 🧾 Rekodi historia
        History::create([
            'user' => Auth::user()->name,
            'action' => 'Amefuta Matumizi',
            'details' => "Aina: {$aina}, Gharama: {$gharama} TZS",
        ]);

        return redirect()->route('matumizi.index')->with('success', 'Matumizi yamefutwa kikamilifu.');
    }

    /**
     * Futa aina ya matumizi
     */
    public function destroyAina($id)
    {
        $aina = AinaYaMatumizi::findOrFail($id);

        if ($aina->company_id !== Auth::user()->company_id) {
            abort(403, 'Huna ruhusa ya kufuta aina hii ya matumizi.');
        }

        // Check if this expense type is being used
        $matumiziCount = Matumizi::where('company_id', Auth::user()->company_id)
            ->where('aina', $aina->jina)
            ->count();

        if ($matumiziCount > 0) {
            return redirect()->back()->with('error', 'Huwezi kufuta aina hii ya matumizi kwa sababu inatumika kwenye matumizi ' . $matumiziCount . '.');
        }

        $aina->delete();

        // 🧾 Rekodi historia
        History::create([
            'user' => Auth::user()->name,
            'action' => 'Amefuta Aina ya Matumizi',
            'details' => "Aina: {$aina->jina}",
        ]);

        return redirect()->route('matumizi.index')->with('success', 'Aina ya matumizi imefutwa kikamilifu.');
    }
}