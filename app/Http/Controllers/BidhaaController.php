<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidhaa;
use Illuminate\Support\Facades\Auth;

class BidhaaController extends Controller
{
    /**
     * Get the current user (check both guards)
     */
    private function getCurrentUser()
    {
        // Try web guard first (for boss/admin)
        $user = Auth::guard('web')->user();
        
        // If not found, try mfanyakazi guard
        if (!$user) {
            $user = Auth::guard('mfanyakazi')->user();
        }
        
        return $user;
    }
    
    /**
     * Get company ID from current user
     */
    private function getCompanyId()
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            abort(403, 'Unauthorized - Please login');
        }
        
        // For mfanyakazi, get company_id from their record
        // For boss, get company_id from user table
        if (Auth::guard('mfanyakazi')->check()) {
            return $user->company_id; // mfanyakazi has company_id directly
        } else {
            return $user->company_id; // boss also has company_id
        }
    }
    
    /**
     * Orodha ya bidhaa
     */
    public function index()
    {
        $companyId = $this->getCompanyId();

        // Show only products belonging to this company
        $bidhaa = Bidhaa::where('company_id', $companyId)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('bidhaa.index', compact('bidhaa'));
    }

    /**
     * Hifadhi bidhaa mpya (normal form)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jina' => 'required|string|max:255',
            'aina' => 'required|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|integer|min:1',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0|gte:bei_nunua',
            'expiry' => 'required|date',
            'barcode' => 'nullable|string|max:255|unique:bidhaas,barcode',
        ]);

        $validated['company_id'] = $this->getCompanyId();

        Bidhaa::create($validated);

        return redirect()->route('bidhaa.index')->with('success', 'Bidhaa imeongezwa kikamilifu.');
    }

    /**
     * Hifadhi bidhaa kwa kutumia barcode
     */
    public function storeBarcode(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:255',
            'jina' => 'required|string|max:255',
            'aina' => 'nullable|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|integer|min:1',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0|gte:bei_nunua',
            'expiry' => 'nullable|date',
        ]);

        $companyId = $this->getCompanyId();

        $existing = Bidhaa::where('barcode', $validated['barcode'])
                          ->where('company_id', $companyId)
                          ->first();

        if ($existing) {
            $existing->update([
                'idadi' => $existing->idadi + $validated['idadi'],
                'bei_nunua' => $validated['bei_nunua'],
                'bei_kuuza' => $validated['bei_kuuza'],
                'expiry' => $validated['expiry'] ?? $existing->expiry,
            ]);

            return redirect()->route('bidhaa.index')
                ->with('success', 'Bidhaa imeongezwa kupitia barcode (idadi imeboreshwa).');
        }

        $validated['company_id'] = $companyId;
        Bidhaa::create($validated);

        return redirect()->route('bidhaa.index')
            ->with('success', 'Bidhaa mpya imehifadhiwa kwa barcode.');
    }

    /**
     * Tafuta bidhaa kwa kutumia barcode (kwa Mauzo)
     */
    public function tafutaBarcode($barcode)
    {
        $companyId = $this->getCompanyId();

        $bidhaa = Bidhaa::where('barcode', $barcode)
                        ->where('company_id', $companyId)
                        ->first();

        if (!$bidhaa) {
            return response()->json(['message' => 'Bidhaa haijapatikana'], 404);
        }

        return response()->json($bidhaa);
    }

    /**
     * Rekebisha bidhaa
     */
    public function update(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $bidhaa = Bidhaa::where('id', $id)
                        ->where('company_id', $companyId)
                        ->firstOrFail();

        $validated = $request->validate([
            'jina' => 'required|string|max:255',
            'aina' => 'required|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|integer|min:1',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0|gte:bei_nunua',
            'expiry' => 'required|date',
            'barcode' => 'nullable|string|max:255|unique:bidhaas,barcode,' . $bidhaa->id,
        ]);

        $bidhaa->update($validated);

        return redirect()->route('bidhaa.index')->with('success', 'Bidhaa imerekebishwa kikamilifu.');
    }

    /**
     * Futa bidhaa
     */
    public function destroy($id)
    {
        $companyId = $this->getCompanyId();

        $bidhaa = Bidhaa::where('id', $id)
                        ->where('company_id', $companyId)
                        ->firstOrFail();

        $bidhaa->delete();

        return redirect()->route('bidhaa.index')->with('success', 'Bidhaa imefutwa kikamilifu.');
    }

    /**
     * Pakua mfano wa faili la Excel (CSV)
     */
    public function downloadSample()
    {
        $filename = "sampuli_bidhaa.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Jina', 'Aina', 'Kipimo', 'Idadi', 'Bei_Nunua', 'Bei_Kuuza', 'Expiry', 'Barcode'];
        $sample = ['Soda', 'Vinywaji', '500ml', '100', '600', '1000', '2025-12-31', '1234567890123'];

        $callback = function() use ($columns, $sample) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sample);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Pakia CSV na hifadhi bidhaa
     */
    public function uploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $companyId = $this->getCompanyId();
        
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        fgetcsv($file); // skip header row

        $errors = [];
        $successCount = 0;
        $lineNumber = 1;

        while (($row = fgetcsv($file)) !== false) {
            $lineNumber++;
            if (empty(array_filter($row))) continue;
            if (count($row) < 8) {
                $errors[] = "Mstari $lineNumber: hauna safu zote 8 zinazohitajika.";
                continue;
            }

            [$jina, $aina, $kipimo, $idadi, $bei_nunua, $bei_kuuza, $expiryRaw, $barcode] = $row;

            if (!$jina || !$aina || !$kipimo || !$idadi || !$bei_nunua || !$bei_kuuza || !$expiryRaw || !$barcode) {
                $errors[] = "Mstari $lineNumber: tafadhali jaza sehemu zote muhimu.";
                continue;
            }

            if (!is_numeric($idadi) || !is_numeric($bei_nunua) || !is_numeric($bei_kuuza)) {
                $errors[] = "Mstari $lineNumber: idadi, bei nunua, na bei kuuza lazima ziwe namba.";
                continue;
            }

            if ((float)$bei_kuuza < (float)$bei_nunua) {
                $errors[] = "Mstari $lineNumber: bei kuuza ($bei_kuuza) haiwezi kuwa chini ya bei nunua ($bei_nunua).";
                continue;
            }

            $expiry = null;
            foreach (['Y-m-d', 'd/m/Y', 'm/d/Y'] as $format) {
                $date = \DateTime::createFromFormat($format, trim($expiryRaw));
                if ($date) {
                    $expiry = $date->format('Y-m-d');
                    break;
                }
            }

            if (!$expiry) {
                $errors[] = "Mstari $lineNumber: tarehe ya expiry si sahihi.";
                continue;
            }

            if (Bidhaa::where('barcode', $barcode)
                      ->where('company_id', $companyId)
                      ->exists()) {
                $errors[] = "Mstari $lineNumber: barcode $barcode tayari ipo.";
                continue;
            }

            Bidhaa::create([
                'company_id' => $companyId,
                'jina' => $jina,
                'aina' => $aina,
                'kipimo' => $kipimo,
                'idadi' => (int)$idadi,
                'bei_nunua' => (float)$bei_nunua,
                'bei_kuuza' => (float)$bei_kuuza,
                'expiry' => $expiry,
                'barcode' => $barcode,
            ]);

            $successCount++;
        }

        fclose($file);

        return back()->with([
            'errorsList' => $errors,
            'successCount' => $successCount,
        ]);
    }
}