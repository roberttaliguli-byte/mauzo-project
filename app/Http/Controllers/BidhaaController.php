<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidhaa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

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
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $perPage = $request->input('per_page', 10);

        $query = Bidhaa::where('company_id', $companyId);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('aina', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%")
                  ->orWhere('kipimo', 'LIKE', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'available':
                    $query->where('idadi', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('idadi', '<', 10)->where('idadi', '>', 0);
                    break;
                case 'expired':
                    $query->where('expiry', '<', now());
                    break;
                case 'no_stock':
                    $query->where('idadi', 0);
                    break;
            }
        }

        $bidhaa = $query->orderBy('created_at', 'desc')
                       ->paginate($perPage)
                       ->appends($request->except('page'));

        // Get stats for all products
        $totalProducts = Bidhaa::where('company_id', $companyId)->count();
        $availableProducts = Bidhaa::where('company_id', $companyId)->where('idadi', '>', 0)->count();
        $lowStockProducts = Bidhaa::where('company_id', $companyId)->where('idadi', '<', 10)->where('idadi', '>', 0)->count();
        $expiredProducts = Bidhaa::where('company_id', $companyId)->where('expiry', '<', now())->count();

        // PDF Export
        if ($request->has('export') && $request->export === 'pdf') {
            $data = [
                'bidhaa' => $bidhaa->items(),
                'title' => 'Orodha ya Bidhaa',
                'date' => now()->format('d/m/Y'),
                'company' => $this->getCurrentUser()->company->name ?? 'Kampuni'
            ];
            
            $pdf = Pdf::loadView('bidhaa.pdf', $data);
            return $pdf->download('bidhaa-' . date('Y-m-d') . '.pdf');
        }

        // AJAX response for search
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('bidhaa.partials.table', compact('bidhaa'))->render()
            ]);
        }

        return view('bidhaa.index', compact('bidhaa', 'totalProducts', 'availableProducts', 'lowStockProducts', 'expiredProducts'));
    }
    
    /**
     * Hifadhi bidhaa mpya (normal form)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jina' => 'required|string|max:255',
            'aina' => 'required|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|integer|min:0',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'expiry' => 'nullable|date|after_or_equal:today',
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('bidhaas', 'barcode')->where(function ($query) {
                    return $query->where('company_id', $this->getCompanyId());
                })
            ],
        ], [
            'jina.required' => 'Jina la bidhaa linahitajika',
            'aina.required' => 'Aina ya bidhaa inahitajika',
            'idadi.required' => 'Idadi ya bidhaa inahitajika',
            'bei_nunua.required' => 'Bei ya kununua inahitajika',
            'bei_kuuza.required' => 'Bei ya kuuza inahitajika',
            'expiry.after_or_equal' => 'Tarehe ya mwisho haiwezi kuwa ya zamani',
            'barcode.unique' => 'Barcode tayari ipo kwenye mfumo',
        ]);

        // Validate that selling price is not lower than buying price
        $validator->after(function ($validator) use ($request) {
            if ($request->bei_kuuza < $request->bei_nunua) {
                $validator->errors()->add('bei_kuuza', 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua');
            }
        });

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hitilafu katika uthibitishaji',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $validated['company_id'] = $this->getCompanyId();

        Bidhaa::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bidhaa imeongezwa kikamilifu!'
            ]);
        }

        return redirect()->route('bidhaa.index')
            ->with('success', 'Bidhaa imeongezwa kikamilifu!');
    }

    /**
     * Hifadhi bidhaa kwa kutumia barcode
     */
    public function storeBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string|max:255',
            'jina' => 'required|string|max:255',
            'aina' => 'nullable|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|integer|min:1',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->bei_kuuza < $request->bei_nunua) {
                $validator->errors()->add('bei_kuuza', 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $this->getCompanyId();
        $validated = $validator->validated();

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

            return response()->json([
                'success' => true,
                'message' => 'Bidhaa imeongezwa kupitia barcode (idadi imeboreshwa).'
            ]);
        }

        $validated['company_id'] = $companyId;
        Bidhaa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bidhaa mpya imehifadhiwa kwa barcode.'
        ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Bidhaa haijapatikana'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bidhaa
        ]);
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

        $validator = Validator::make($request->all(), [
            'jina' => 'required|string|max:255',
            'aina' => 'required|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|integer|min:0',
            'bei_nunua' => 'required|numeric|min:0',
            'bei_kuuza' => 'required|numeric|min:0',
            'expiry' => 'nullable|date|after_or_equal:today',
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('bidhaas', 'barcode')
                    ->where(function ($query) use ($companyId) {
                        return $query->where('company_id', $companyId);
                    })
                    ->ignore($bidhaa->id)
            ],
        ], [
            'jina.required' => 'Jina la bidhaa linahitajika',
            'aina.required' => 'Aina ya bidhaa inahitajika',
            'idadi.required' => 'Idadi ya bidhaa inahitajika',
            'bei_nunua.required' => 'Bei ya kununua inahitajika',
            'bei_kuuza.required' => 'Bei ya kuuza inahitajika',
            'expiry.after_or_equal' => 'Tarehe ya mwisho haiwezi kuwa ya zamani',
            'barcode.unique' => 'Barcode tayari ipo kwenye mfumo',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->bei_kuuza < $request->bei_nunua) {
                $validator->errors()->add('bei_kuuza', 'Bei ya kuuza haiwezi kuwa chini ya bei ya kununua');
            }
        });

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hitilafu katika uthibitishaji',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $bidhaa->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bidhaa imerekebishwa kikamilifu!'
            ]);
        }

        return redirect()->route('bidhaa.index')
            ->with('success', 'Bidhaa imerekebishwa kikamilifu!');
    }

    /**
     * Futa bidhaa
     */
    public function destroy($id, Request $request)
    {
        $companyId = $this->getCompanyId();

        $bidhaa = Bidhaa::where('id', $id)
                        ->where('company_id', $companyId)
                        ->firstOrFail();

        $bidhaa->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bidhaa imefutwa kikamilifu!'
            ]);
        }

        return redirect()->route('bidhaa.index')
            ->with('success', 'Bidhaa imefutwa kikamilifu!');
    }

    /**
     * Pakua mfano wa faili la CSV
     */
    public function downloadSample()
    {
        $filename = "sampuli_bidhaa_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Jina', 'Aina', 'Kipimo', 'Idadi', 'Bei_Nunua', 'Bei_Kuuza', 'Expiry', 'Barcode'];
        $sample1 = ['Soda', 'Vinywaji', '500ml', '100', '600', '1000', '2025-12-31', '1234567890123'];
        $sample2 = ['Mchele', 'Chakula', '1kg', '50', '2500', '3500', '', ''];
        $sample3 = ['Sabuni', 'Usafi', '400g', '30', '1200', '1800', '2024-12-01', ''];

        $callback = function() use ($columns, $sample1, $sample2, $sample3) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper encoding
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            fputcsv($file, $sample1);
            fputcsv($file, $sample2);
            fputcsv($file, $sample3);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Pakia Excel/CSV na hifadhi bidhaa
     */
    public function uploadCSV(Request $request)
    {
        // Validate file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csv_file.required' => 'Tafadhali chagua faili',
            'csv_file.mimes' => 'Aina ya faili hairuhusiwi. Tumia .csv au .txt',
            'csv_file.max' => 'Faili ni kubwa sana. Ukubwa upeo ni 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika faili',
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $this->getCompanyId();
        $errors = [];
        $successCount = 0;
        $lineNumber = 0;
        $skippedRows = 0;
        $totalRows = 0;

        try {
            $file = $request->file('csv_file');
            
            // Process CSV file
            $rows = $this->processCSVFile($file);
            
            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faili liko tupu au halina data.',
                    'data' => [
                        'successCount' => 0,
                        'errorCount' => 1,
                        'skippedRows' => 0,
                        'totalRows' => 0,
                        'errors' => ['Faili halina data au muundo sio sahihi']
                    ]
                ]);
            }
            
            $totalRows = count($rows);
            
            // Process each row
            foreach ($rows as $index => $rowData) {
                $lineNumber = $index + 2; // +2 because index starts at 0 and header is row 1
                
                // Skip empty rows
                if (empty(array_filter($rowData, function($value) { 
                    return $value !== '' && $value !== null; 
                }))) {
                    $skippedRows++;
                    continue;
                }
                
                // Validate required fields
                $requiredFields = ['Jina', 'Aina', 'Idadi', 'Bei_Nunua', 'Bei_Kuuza'];
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!isset($rowData[$field]) || trim($rowData[$field]) === '') {
                        $missingFields[] = $field;
                    }
                }
                
                if (!empty($missingFields)) {
                    $fieldsList = implode(', ', $missingFields);
                    $errors[] = "Mstari {$lineNumber}: Sehemu zinazohitajika hazipo ({$fieldsList})";
                    continue;
                }
                
                // Validate numeric fields
                $idadi = trim($rowData['Idadi']);
                if (!is_numeric($idadi) || (int)$idadi < 0) {
                    $errors[] = "Mstari {$lineNumber}: Idadi '{$idadi}' si sahihi. Lazima iwe namba nzuri.";
                    continue;
                }
                
                $beiNunua = trim($rowData['Bei_Nunua']);
                if (!is_numeric($beiNunua) || (float)$beiNunua < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua '{$beiNunua}' si sahihi. Lazima iwe namba nzuri.";
                    continue;
                }
                
                $beiKuuza = trim($rowData['Bei_Kuuza']);
                if (!is_numeric($beiKuuza) || (float)$beiKuuza < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kuuza '{$beiKuuza}' si sahihi. Lazima iwe namba nzuri.";
                    continue;
                }
                
                if ((float)$beiKuuza < (float)$beiNunua) {
                    $errors[] = "Mstari {$lineNumber}: Bei kuuza ({$beiKuuza}) haiwezi kuwa chini ya bei nunua ({$beiNunua}).";
                    continue;
                }
                
                // Parse expiry date
                $expiry = null;
                if (!empty($rowData['Expiry']) && strtolower(trim($rowData['Expiry'])) !== 'n/a') {
                    $expiryDate = trim($rowData['Expiry']);
                    
                    // Try different date formats
                    $parsedDate = false;
                    $dateFormats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];
                    
                    foreach ($dateFormats as $format) {
                        $date = \DateTime::createFromFormat($format, $expiryDate);
                        if ($date && $date->format($format) === $expiryDate) {
                            $expiry = $date->format('Y-m-d');
                            $parsedDate = true;
                            break;
                        }
                    }
                    
                    if (!$parsedDate) {
                        // Try strtotime as fallback
                        $timestamp = strtotime($expiryDate);
                        if ($timestamp !== false) {
                            $expiry = date('Y-m-d', $timestamp);
                            $parsedDate = true;
                        }
                    }
                    
                    if (!$parsedDate) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$expiryDate}' si sahihi. Tumia muundo YYYY-MM-DD";
                        continue;
                    }
                    
                    // Check if expiry is in the past
                    if (strtotime($expiry) < strtotime('today')) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$expiry}' imepita.";
                        continue;
                    }
                }
                
                // Check barcode uniqueness
                $barcode = null;
                if (!empty($rowData['Barcode']) && strtolower(trim($rowData['Barcode'])) !== 'n/a') {
                    $barcode = trim($rowData['Barcode']);
                    
                    // Check if barcode already exists
                    if ($barcode) {
                        $existing = Bidhaa::where('barcode', $barcode)
                                        ->where('company_id', $companyId)
                                        ->exists();
                        
                        if ($existing) {
                            $errors[] = "Mstari {$lineNumber}: Barcode '{$barcode}' tayari ipo kwenye mfumo.";
                            continue;
                        }
                    }
                }
                
                try {
                    // Check if product with same name and type already exists
                    $existingProduct = Bidhaa::where('jina', trim($rowData['Jina']))
                                            ->where('aina', trim($rowData['Aina']))
                                            ->where('company_id', $companyId)
                                            ->first();
                    
                    if ($existingProduct) {
                        // Update existing product (add stock)
                        $existingProduct->update([
                            'idadi' => $existingProduct->idadi + (int)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                            'expiry' => $expiry ?: $existingProduct->expiry,
                            'barcode' => $barcode ?: $existingProduct->barcode,
                            'kipimo' => !empty($rowData['Kipimo']) ? trim($rowData['Kipimo']) : $existingProduct->kipimo,
                        ]);
                        
                        $successCount++;
                    } else {
                        // Create new product
                        Bidhaa::create([
                            'company_id' => $companyId,
                            'jina' => trim($rowData['Jina']),
                            'aina' => trim($rowData['Aina']),
                            'kipimo' => !empty($rowData['Kipimo']) ? trim($rowData['Kipimo']) : null,
                            'idadi' => (int)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                            'expiry' => $expiry,
                            'barcode' => $barcode,
                        ]);
                        
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Mstari {$lineNumber}: Hitilafu ya ndani - " . $e->getMessage();
                    \Log::error('CSV Upload Error: ' . $e->getMessage(), [
                        'row' => $rowData,
                        'line' => $lineNumber
                    ]);
                }
            }
            
            // Prepare response
            $response = [
                'success' => true,
                'message' => "Upakiaji umekamilika!",
                'data' => [
                    'successCount' => $successCount,
                    'errorCount' => count($errors),
                    'skippedRows' => $skippedRows,
                    'totalRows' => $totalRows,
                    'errors' => array_slice($errors, 0, 20) // Limit to first 20 errors
                ]
            ];
            
            if (count($errors) > 0 && $successCount === 0) {
                $response['success'] = false;
                $response['message'] = "Upakiaji umeshindwa. Hakuna bidhaa zilizoongezwa.";
            } elseif (count($errors) > 0) {
                $response['message'] = "Upakiaji umekamilika kwa hitilafu " . count($errors);
            } elseif ($successCount === 0) {
                $response['success'] = false;
                $response['message'] = "Hakuna bidhaa zilizoongezwa. Hakikisha data yako iko sahihi.";
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('CSV Upload Processing Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika usindikaji wa faili: ' . $e->getMessage(),
                'data' => [
                    'successCount' => 0,
                    'errorCount' => 1,
                    'skippedRows' => 0,
                    'totalRows' => 0,
                    'errors' => ['Hitilafu ya jumla: ' . $e->getMessage()]
                ]
            ], 500);
        }
    }
    
    /**
     * Process CSV file
     */
    private function processCSVFile($file)
    {
        $rows = [];
        $header = null;
        
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Read the file line by line
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Skip completely empty rows
                if ($row === null || count(array_filter($row, function($value) { 
                    return $value !== '' && $value !== null; 
                })) === 0) {
                    continue;
                }
                
                // Trim all values
                $row = array_map('trim', $row);
                
                if ($header === null) {
                    // First non-empty row is header
                    $header = $row;
                    
                    // Validate header columns - allow flexible column names
                    $expectedColumns = ['Jina', 'Aina', 'Kipimo', 'Idadi', 'Bei_Nunua', 'Bei_Kuuza', 'Expiry', 'Barcode'];
                    $headerLower = array_map('strtolower', $header);
                    $expectedLower = array_map('strtolower', $expectedColumns);
                    
                    $missing = array_diff($expectedLower, $headerLower);
                    if (!empty($missing)) {
                        fclose($handle);
                        throw new \Exception('Muundo wa faili sio sahihi. Hakikisha una safu zote zinazohitajika: ' . implode(', ', $expectedColumns));
                    }
                    
                    continue;
                }
                
                // Combine header with row values
                $rowData = [];
                foreach ($header as $index => $column) {
                    $rowData[$column] = isset($row[$index]) ? $row[$index] : '';
                }
                
                $rows[] = $rowData;
            }
            fclose($handle);
        }
        
        return $rows;
    }
}