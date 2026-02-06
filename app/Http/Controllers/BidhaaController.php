<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidhaa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

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
     * Pakua mfano wa faili la Excel/CSV
     */
    public function downloadSample()
    {
        $filename = "sampuli_bidhaa_" . date('Y-m-d') . ".xlsx";
        
        // For Excel file, we need to use PhpSpreadsheet
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header
            $sheet->setCellValue('A1', 'Jina');
            $sheet->setCellValue('B1', 'Aina');
            $sheet->setCellValue('C1', 'Kipimo');
            $sheet->setCellValue('D1', 'Idadi');
            $sheet->setCellValue('E1', 'Bei_Nunua');
            $sheet->setCellValue('F1', 'Bei_Kuuza');
            $sheet->setCellValue('G1', 'Expiry');
            $sheet->setCellValue('H1', 'Barcode');
            
            // Set sample data
            $samples = [
                ['Soda', 'Vinywaji', '500ml', 100, 600, 1000, '2025-12-31', '1234567890123'],
                ['Mchele', 'Chakula', '1kg', 50, 2500, 3500, '', ''],
                ['Sabuni', 'Usafi', '400g', 30, 1200, 1800, '2024-12-01', ''],
                ['Maji', 'Vinywaji', '1.5L', 200, 500, 800, '', '987654321'],
            ];
            
            $row = 2;
            foreach ($samples as $sample) {
                $sheet->setCellValue('A' . $row, $sample[0]);
                $sheet->setCellValue('B' . $row, $sample[1]);
                $sheet->setCellValue('C' . $row, $sample[2]);
                $sheet->setCellValue('D' . $row, $sample[3]);
                $sheet->setCellValue('E' . $row, $sample[4]);
                $sheet->setCellValue('F' . $row, $sample[5]);
                $sheet->setCellValue('G' . $row, $sample[6]);
                $sheet->setCellValue('H' . $row, $sample[7]);
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add some styling
            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            $sheet->getStyle('A1:H1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE8F5E8');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            // Fallback to CSV if Excel fails
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
            $sample4 = ['Maji', 'Vinywaji', '1.5L', '200', '500', '800', '', '987654321'];

            $callback = function() use ($columns, $sample1, $sample2, $sample3, $sample4) {
                $file = fopen('php://output', 'w');
                // Add UTF-8 BOM for proper encoding
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, $columns);
                fputcsv($file, $sample1);
                fputcsv($file, $sample2);
                fputcsv($file, $sample3);
                fputcsv($file, $sample4);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    /**
     * Pakia Excel/CSV na hifadhi bidhaa
     */
    public function uploadExcel(Request $request)
    {
        // Validate file - support both Excel and CSV
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240', // 10MB max
        ], [
            'excel_file.required' => 'Tafadhali chagua faili',
            'excel_file.mimes' => 'Aina ya faili hairuhusiwi. Tumia: CSV, TXT, XLS, XLSX',
            'excel_file.max' => 'Faili ni kubwa sana. Ukubwa upeo ni 10MB',
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
            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Process based on file type
            if (in_array($extension, ['xls', 'xlsx'])) {
                $rows = $this->processExcelFile($file);
            } else {
                $rows = $this->processCSVFile($file);
            }
            
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
            
            // Start transaction for data consistency
            DB::beginTransaction();
            
            // Process each row
            foreach ($rows as $index => $rowData) {
                $lineNumber = $index + 2; // +2 because index starts at 0 and header is row 1
                
                // Skip empty rows
                if (empty(array_filter($rowData, function($value) { 
                    return $value !== '' && $value !== null && trim($value) !== ''; 
                }))) {
                    $skippedRows++;
                    continue;
                }
                
                // Normalize column names (case-insensitive)
                $normalizedRow = [];
                foreach ($rowData as $key => $value) {
                    $normalizedKey = strtolower(trim($key));
                    $normalizedRow[$normalizedKey] = trim($value);
                }
                
                // Map column names
                $columnMapping = [
                    'jina' => 'Jina',
                    'aina' => 'Aina',
                    'kipimo' => 'Kipimo',
                    'idadi' => 'Idadi',
                    'bei_nunua' => 'Bei_Nunua',
                    'bei nunua' => 'Bei_Nunua',
                    'beinunua' => 'Bei_Nunua',
                    'bei_kuuza' => 'Bei_Kuuza',
                    'bei kuuza' => 'Bei_Kuuza',
                    'beikuuza' => 'Bei_Kuuza',
                    'expiry' => 'Expiry',
                    'expirydate' => 'Expiry',
                    'tarehe ya mwisho' => 'Expiry',
                    'barcode' => 'Barcode',
                    'namba ya mfumo' => 'Barcode',
                ];
                
                // Get values with fallback
                $jina = '';
                foreach (['jina', 'name', 'bidhaa'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $jina = $normalizedRow[$key];
                        break;
                    }
                }
                
                $aina = '';
                foreach (['aina', 'category', 'aina ya bidhaa'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $aina = $normalizedRow[$key];
                        break;
                    }
                }
                
                $idadi = '';
                foreach (['idadi', 'quantity', 'stock', 'kiasi'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $idadi = $normalizedRow[$key];
                        break;
                    }
                }
                
                $beiNunua = '';
                foreach (['bei_nunua', 'bei nunua', 'beinunua', 'buying price', 'cost price'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $beiNunua = $normalizedRow[$key];
                        break;
                    }
                }
                
                $beiKuuza = '';
                foreach (['bei_kuuza', 'bei kuuza', 'beikuuza', 'selling price', 'sale price'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $beiKuuza = $normalizedRow[$key];
                        break;
                    }
                }
                
                $kipimo = '';
                foreach (['kipimo', 'unit', 'measure', 'kiasi cha kipimo'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $kipimo = $normalizedRow[$key];
                        break;
                    }
                }
                
                $expiry = '';
                foreach (['expiry', 'expirydate', 'tarehe ya mwisho', 'expiry date'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $expiry = $normalizedRow[$key];
                        break;
                    }
                }
                
                $barcode = '';
                foreach (['barcode', 'namba ya mfumo', 'code'] as $key) {
                    if (isset($normalizedRow[$key]) && $normalizedRow[$key] !== '') {
                        $barcode = $normalizedRow[$key];
                        break;
                    }
                }
                
                // Validate required fields
                if (empty($jina)) {
                    $errors[] = "Mstari {$lineNumber}: Jina la bidhaa linakosekana";
                    continue;
                }
                
                if (empty($aina)) {
                    $errors[] = "Mstari {$lineNumber}: Aina ya bidhaa inakosekana";
                    continue;
                }
                
                if (empty($idadi)) {
                    $errors[] = "Mstari {$lineNumber}: Idadi ya bidhaa inakosekana";
                    continue;
                }
                
                if (empty($beiNunua)) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua inakosekana";
                    continue;
                }
                
                if (empty($beiKuuza)) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kuuza inakosekana";
                    continue;
                }
                
                // Validate numeric fields
                if (!is_numeric($idadi) || (int)$idadi < 0) {
                    $errors[] = "Mstari {$lineNumber}: Idadi '{$idadi}' si sahihi. Lazima iwe namba nzuri.";
                    continue;
                }
                
                if (!is_numeric($beiNunua) || (float)$beiNunua < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua '{$beiNunua}' si sahihi. Lazima iwe namba nzuri.";
                    continue;
                }
                
                if (!is_numeric($beiKuuza) || (float)$beiKuuza < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kuuza '{$beiKuuza}' si sahihi. Lazima iwe namba nzuri.";
                    continue;
                }
                
                if ((float)$beiKuuza < (float)$beiNunua) {
                    $errors[] = "Mstari {$lineNumber}: Bei kuuza ({$beiKuuza}) haiwezi kuwa chini ya bei nunua ({$beiNunua}).";
                    continue;
                }
                
                // Parse expiry date (optional)
                $expiryDate = null;
                if (!empty($expiry) && strtolower($expiry) !== 'n/a' && strtolower($expiry) !== 'na') {
                    $parsedDate = $this->parseDate($expiry);
                    if (!$parsedDate) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$expiry}' si sahihi. Tumia muundo YYYY-MM-DD, DD/MM/YYYY au DD-MM-YYYY";
                        continue;
                    }
                    
                    // Check if expiry is in the past
                    if (strtotime($parsedDate) < strtotime('today')) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$parsedDate}' imepita.";
                        continue;
                    }
                    
                    $expiryDate = $parsedDate;
                }
                
                // Check barcode uniqueness (optional)
                if (!empty($barcode)) {
                    // Remove any whitespace
                    $barcode = trim($barcode);
                    
                    // Check if barcode already exists
                    $existingBarcode = Bidhaa::where('barcode', $barcode)
                                            ->where('company_id', $companyId)
                                            ->exists();
                    
                    if ($existingBarcode) {
                        $errors[] = "Mstari {$lineNumber}: Barcode '{$barcode}' tayari ipo kwenye mfumo.";
                        continue;
                    }
                }
                
                try {
                    // Check if product with same name and type already exists
                    $existingProduct = Bidhaa::where('jina', $jina)
                                            ->where('aina', $aina)
                                            ->where('company_id', $companyId)
                                            ->first();
                    
                    if ($existingProduct) {
                        // Update existing product (add stock)
                        $existingProduct->update([
                            'idadi' => $existingProduct->idadi + (int)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                            'expiry' => $expiryDate ?: $existingProduct->expiry,
                            'barcode' => !empty($barcode) ? $barcode : $existingProduct->barcode,
                            'kipimo' => !empty($kipimo) ? $kipimo : $existingProduct->kipimo,
                        ]);
                        
                        $successCount++;
                    } else {
                        // Create new product
                        Bidhaa::create([
                            'company_id' => $companyId,
                            'jina' => $jina,
                            'aina' => $aina,
                            'kipimo' => !empty($kipimo) ? $kipimo : null,
                            'idadi' => (int)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                            'expiry' => $expiryDate,
                            'barcode' => !empty($barcode) ? $barcode : null,
                        ]);
                        
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Mstari {$lineNumber}: Hitilafu ya ndani - " . $e->getMessage();
                    \Log::error('Excel Upload Error: ' . $e->getMessage(), [
                        'row' => $rowData,
                        'line' => $lineNumber
                    ]);
                }
            }
            
            // Commit transaction
            DB::commit();
            
            // Prepare response
            $response = [
                'success' => true,
                'message' => "Upakiaji umekamilika!",
                'data' => [
                    'successCount' => $successCount,
                    'errorCount' => count($errors),
                    'skippedRows' => $skippedRows,
                    'totalRows' => $totalRows,
                    'errors' => array_slice($errors, 0, 50) // Limit to first 50 errors
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
            // Rollback on error
            DB::rollBack();
            \Log::error('Excel Upload Processing Error: ' . $e->getMessage());
            
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
     * Process Excel file (XLS, XLSX)
     */
    private function processExcelFile($file)
    {
        $rows = [];
        
        try {
            // Check if PhpSpreadsheet is available
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library haijapatikana. Tafadhali install kwa kutumia: composer require phpoffice/phpspreadsheet');
            }
            
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            
            // Get header row (first row)
            $header = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . '1')->getValue();
                $header[] = trim($cellValue);
            }
            
            // Process data rows
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                $colIndex = 0;
                $isEmptyRow = true;
                
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getValue();
                    
                    // Clean up the value
                    if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                        $cellValue = $cellValue->getPlainText();
                    }
                    
                    if ($cellValue !== null && trim($cellValue) !== '') {
                        $isEmptyRow = false;
                    }
                    
                    $columnName = isset($header[$colIndex]) ? trim($header[$colIndex]) : 'Column' . $colIndex;
                    $rowData[$columnName] = trim($cellValue ?? '');
                    $colIndex++;
                }
                
                // Skip empty rows
                if (!$isEmptyRow) {
                    $rows[] = $rowData;
                }
            }
            
        } catch (\Exception $e) {
            throw new \Exception('Hitilafu katika kusoma faili la Excel: ' . $e->getMessage());
        }
        
        return $rows;
    }
    
    /**
     * Process CSV/TXT file
     */
    private function processCSVFile($file)
    {
        $rows = [];
        $header = null;
        
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Try to detect delimiter
            $firstLine = fgets($handle);
            rewind($handle);
            
            $delimiter = ',';
            if (strpos($firstLine, ';') !== false) {
                $delimiter = ';';
            } elseif (strpos($firstLine, "\t") !== false) {
                $delimiter = "\t";
            }
            
            // Read the file line by line
            $lineNumber = 0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $lineNumber++;
                
                // Skip completely empty rows
                if ($row === null || count(array_filter($row, function($value) { 
                    return $value !== '' && $value !== null && trim($value) !== ''; 
                })) === 0) {
                    continue;
                }
                
                // Trim all values
                $row = array_map('trim', $row);
                
                if ($header === null) {
                    // First non-empty row is header
                    $header = $row;
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
    
    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        // Remove any time portion
        $dateString = preg_split('/[\s,]+/', $dateString)[0];
        
        // Try different date formats
        $formats = [
            'Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y',
            'Y/m/d', 'Y.m.d', 'd.m.Y', 'Y-m-d H:i:s',
            'd M Y', 'j M Y', 'd F Y', 'j F Y'
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date && $date->format($format) === $dateString) {
                return $date->format('Y-m-d');
            }
        }
        
        // Try strtotime as fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        return null;
    }
}