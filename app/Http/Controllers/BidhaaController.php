<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidhaa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BidhaaController extends Controller
{
    /**
     * Get the current user (check both guards)
     */
    private function getCurrentUser()
    {
        $user = Auth::guard('web')->user();
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
        return $user->company_id;
    }
    
    /**
     * Check if current user is boss/admin (not mfanyakazi)
     */
    private function isBoss()
    {
        return Auth::guard('web')->check();
    }
    
    /**
     * Orodha ya bidhaa
     */
    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $perPage = $request->input('per_page', 10);
        $isBoss = $this->isBoss();

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
                case 'out_of_stock':
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
        $outOfStockProducts = Bidhaa::where('company_id', $companyId)->where('idadi', 0)->count();

        // PDF Export - ALL PRODUCTS (not paginated)
        if ($request->has('export') && $request->export === 'pdf') {
            $allProducts = Bidhaa::where('company_id', $companyId)
                               ->orderBy('jina')
                               ->get();
            
            $data = [
                'bidhaa' => $allProducts,
                'title' => 'Orodha ya Bidhaa Zote',
                'date' => now()->format('d/m/Y'),
                'company' => $this->getCurrentUser()->company->name ?? 'Kampuni',
                'total_count' => $allProducts->count()
            ];
            
            $pdf = Pdf::loadView('bidhaa.pdf', $data);
            return $pdf->download('bidhaa-zote-' . date('Y-m-d') . '.pdf');
        }

        // Excel Export - ALL PRODUCTS (not paginated)
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportExcel();
        }

        // AJAX response for search
        if ($request->ajax() && !$request->has('export')) {
            return response()->json([
                'success' => true,
                'html' => view('bidhaa.partials.table', compact('bidhaa'))->render()
            ]);
        }

        return view('bidhaa.index', compact(
            'bidhaa', 
            'totalProducts', 
            'availableProducts', 
            'lowStockProducts', 
            'expiredProducts',
            'outOfStockProducts',
            'isBoss'
        ));
    }
    
    /**
     * Search all bidhaa (for AJAX search - searches across ALL products)
     */
    public function searchAll(Request $request)
    {
        $companyId = $this->getCompanyId();
        
        $search = trim($request->input('search', ''));
        
        if (empty($search) || strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Tafadhali ingiza angalau herufi 2',
                'data' => []
            ]);
        }
        
        try {
            // NO PAGINATION - get ALL matching products
            $bidhaa = Bidhaa::where('company_id', $companyId)
                ->where(function($query) use ($search) {
                    $query->where('jina', 'LIKE', "%{$search}%")
                          ->orWhere('aina', 'LIKE', "%{$search}%")
                          ->orWhere('barcode', 'LIKE', "%{$search}%")
                          ->orWhere('kipimo', 'LIKE', "%{$search}%");
                })
                ->orderBy('jina')
                ->get();
            
            // Map the results to ensure proper date formatting
            $mappedResults = $bidhaa->map(function($item) {
                // Determine stock status
                $stockStatus = 'in-stock';
                if ($item->idadi == 0) {
                    $stockStatus = 'out-of-stock';
                } elseif ($item->idadi < 10) {
                    $stockStatus = 'low-stock';
                }
                
                // Handle expiry date safely - using the cast from model
                $expiryDate = null;
                if ($item->expiry) {
                    if ($item->expiry instanceof \Carbon\Carbon) {
                        $expiryDate = $item->expiry->format('Y-m-d');
                    } else {
                        // Try to parse if it's a string
                        try {
                            $expiryDate = \Carbon\Carbon::parse($item->expiry)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = $item->expiry;
                        }
                    }
                }
                
                return [
                    'id' => $item->id,
                    'jina' => $item->jina,
                    'aina' => $item->aina,
                    'kipimo' => $item->kipimo,
                    'idadi' => $item->idadi,
                    'bei_nunua' => $item->bei_nunua,
                    'bei_kuuza' => $item->bei_kuuza,
                    'expiry' => $expiryDate,
                    'barcode' => $item->barcode,
                    'stock_status' => $stockStatus,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Utafutaji umekamilika',
                'data' => $mappedResults,
                'count' => $mappedResults->count(),
                'search_term' => $search
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika utafutaji',
                'data' => []
            ], 500);
        }
    }
    
    /**
     * Get product for editing (AJAX endpoint)
     */
    public function editProduct($id)
    {
        try {
            $companyId = $this->getCompanyId();
            
            $product = Bidhaa::where('id', $id)
                            ->where('company_id', $companyId)
                            ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bidhaa haijapatikana'
                ], 404);
            }
            
            // Handle expiry date safely
            $expiryDate = null;
            if ($product->expiry) {
                if ($product->expiry instanceof \Carbon\Carbon) {
                    $expiryDate = $product->expiry->format('Y-m-d');
                } else {
                    try {
                        $expiryDate = \Carbon\Carbon::parse($product->expiry)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiryDate = $product->expiry;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'jina' => $product->jina,
                    'aina' => $product->aina,
                    'kipimo' => $product->kipimo,
                    'idadi' => $product->idadi,
                    'bei_nunua' => $product->bei_nunua,
                    'bei_kuuza' => $product->bei_kuuza,
                    'expiry' => $expiryDate,
                    'barcode' => $product->barcode
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kupakua bidhaa'
            ], 500);
        }
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
            'expiry' => 'nullable|date',
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
            'idadi.min' => 'Idadi haiwezi kuwa chini ya 0',
            'bei_nunua.required' => 'Bei ya kununua inahitajika',
            'bei_kuuza.required' => 'Bei ya kuuza inahitajika',
            'barcode.unique' => 'Barcode tayari ipo',
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
     * Rekebisha bidhaa
     */
    public function update(Request $request, $id)
    {
        // Check if user is boss - mfanyakazi cannot edit
        if (!$this->isBoss()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hurumia, wewe huna ruhusa ya kurekebisha bidhaa. Wasiliana na meneja.'
                ], 403);
            }
            return redirect()->route('bidhaa.index')
                ->with('error', 'Hurumia, wewe huna ruhusa ya kurekebisha bidhaa.');
        }

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
            'expiry' => 'nullable|date',
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
        // Check if user is boss - mfanyakazi cannot delete
        if (!$this->isBoss()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hurumia, wewe huna ruhusa ya kufuta bidhaa. Wasiliana na meneja.'
                ], 403);
            }
            return redirect()->route('bidhaa.index')
                ->with('error', 'Hurumia, wewe huna ruhusa ya kufuta bidhaa.');
        }

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
     * Export ALL products to Excel (XLSX format)
     */
    public function exportExcel()
    {
        $companyId = $this->getCompanyId();
        $company = $this->getCurrentUser()->company->name ?? 'Kampuni';
        
        // Get ALL products - no pagination
        $products = Bidhaa::where('company_id', $companyId)
                         ->orderBy('jina')
                         ->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title and metadata
        $sheet->setTitle('Bidhaa Zote');
        
        // Set headers with exact column names (NO ASTERISKS for easier matching)
        $headers = [
            'A1' => 'Jina la Bidhaa', // Required
            'B1' => 'Aina',           // Required
            'C1' => 'Kipimo',
            'D1' => 'Idadi',           // Required
            'E1' => 'Bei Nunua',        // Required
            'F1' => 'Bei Kuuza',        // Required
            'G1' => 'Expiry Date',
            'H1' => 'Barcode',
            'I1' => 'Faida',
            'J1' => 'Asilimia',
            'K1' => 'Hali ya Hisa'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '047857'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D1FAE5']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '10B981']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Add data rows
        $row = 2;
        foreach ($products as $product) {
            $faida = $product->bei_kuuza - $product->bei_nunua;
            $asilimia = $product->bei_nunua > 0 
                ? round(($faida / $product->bei_nunua) * 100, 2) . '%' 
                : '0%';
            
            $hali = 'Inapatikana';
            if ($product->idadi == 0) {
                $hali = 'Imeisha';
            } elseif ($product->idadi < 10) {
                $hali = 'Inakaribia kuisha';
            }
            
            $sheet->setCellValue('A' . $row, $product->jina);
            $sheet->setCellValue('B' . $row, $product->aina);
            $sheet->setCellValue('C' . $row, $product->kipimo ?? '');
            $sheet->setCellValue('D' . $row, $product->idadi);
            $sheet->setCellValue('E' . $row, $product->bei_nunua);
            $sheet->setCellValue('F' . $row, $product->bei_kuuza);
            $sheet->setCellValue('G' . $row, $product->expiry ? \Carbon\Carbon::parse($product->expiry)->format('Y-m-d') : '');
            $sheet->setCellValue('H' . $row, $product->barcode ?? '');
            $sheet->setCellValue('I' . $row, $faida . ' TZS');
            $sheet->setCellValue('J' . $row, $asilimia);
            $sheet->setCellValue('K' . $row, $hali);
            
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = "bidhaa_zote_" . date('Y-m-d_His') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Pakua mfano wa faili la Excel (XLSX format) - SIMPLIFIED VERSION
     */
    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setTitle('Sampuli');
        
        // Set headers with EXACT column names (NO ASTERISKS, NO EXTRA TEXT)
        // These exact names will be used to match the columns during upload
        $headers = [
            'A1' => 'Jina la Bidhaa', // Required
            'B1' => 'Aina',           // Required
            'C1' => 'Kipimo',
            'D1' => 'Idadi',           // Required
            'E1' => 'Bei Nunua',        // Required
            'F1' => 'Bei Kuuza',        // Required
            'G1' => 'Expiry Date',
            'H1' => 'Barcode'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '047857'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D1FAE5']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '10B981']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        // SIMPLE SAMPLE DATA - just 2 rows for testing
        $sampleData = [
            ['Soda', 'Vinywaji', '500ml', 100, 600, 1000, '2025-12-31', '123456789'],
            ['Mchele', 'Chakula', '1kg', 200, 2500, 3500, '2026-01-15', '']
        ];
        
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->setCellValue('C' . $row, $data[2]);
            $sheet->setCellValue('D' . $row, $data[3]);
            $sheet->setCellValue('E' . $row, $data[4]);
            $sheet->setCellValue('F' . $row, $data[5]);
            $sheet->setCellValue('G' . $row, $data[6]);
            $sheet->setCellValue('H' . $row, $data[7]);
            $row++;
        }
        
        // Style data rows
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ];
        
        $lastRow = $row - 1;
        $sheet->getStyle('A2:H' . $lastRow)->applyFromArray($dataStyle);
        
        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = "sampuli_bidhaa_" . date('Y-m-d') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Pakia Excel/CSV na hifadhi bidhaa
     * If product with same jina, aina, and kipimo exists -> UPDATE (replace quantity with Excel value)
     * Otherwise -> CREATE NEW
     */
    public function uploadExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
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
        $updatedCount = 0;
        $createdCount = 0;

        try {
            $file = $request->file('excel_file');
            
            // Process the file
            $rows = $this->processExcelFile($file);
            
            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faili liko tupu au halina data.',
                    'data' => [
                        'successCount' => 0,
                        'errorCount' => 1,
                        'skippedRows' => 0,
                        'totalRows' => 0,
                        'updatedCount' => 0,
                        'createdCount' => 0,
                        'errors' => ['Faili halina data']
                    ]
                ]);
            }
            
            $totalRows = count($rows);
            
            DB::beginTransaction();
            
            foreach ($rows as $index => $rowData) {
                $lineNumber = $index + 2; // +2 because row 1 is header
                
                // Check if row is empty
                $isEmpty = true;
                foreach ($rowData as $value) {
                    if ($value !== null && trim($value) !== '') {
                        $isEmpty = false;
                        break;
                    }
                }
                
                if ($isEmpty) {
                    $skippedRows++;
                    continue;
                }
                
                // Extract values using direct column names (case-insensitive)
                $jina = $this->getExcelValue($rowData, ['Jina la Bidhaa', 'jina la bidhaa', 'Jina', 'jina']);
                $aina = $this->getExcelValue($rowData, ['Aina', 'aina']);
                $kipimo = $this->getExcelValue($rowData, ['Kipimo', 'kipimo']);
                $idadi = $this->getExcelValue($rowData, ['Idadi', 'idadi']);
                $beiNunua = $this->getExcelValue($rowData, ['Bei Nunua', 'bei nunua', 'Bei', 'bei']);
                $beiKuuza = $this->getExcelValue($rowData, ['Bei Kuuza', 'bei kuuza']);
                $expiry = $this->getExcelValue($rowData, ['Expiry Date', 'expiry date', 'Expiry', 'expiry']);
                $barcode = $this->getExcelValue($rowData, ['Barcode', 'barcode']);
                
                // Debug log
                \Log::info("Row {$lineNumber}: jina={$jina}, aina={$aina}, idadi={$idadi}, beiNunua={$beiNunua}, beiKuuza={$beiKuuza}");
                
                // Validate required fields
                if (empty($jina)) {
                    $errors[] = "Mstari {$lineNumber}: Jina la bidhaa linakosekana";
                    continue;
                }
                
                if (empty($aina)) {
                    $errors[] = "Mstari {$lineNumber}: Aina ya bidhaa inakosekana";
                    continue;
                }
                
                // Handle idadi (default to 0 if empty)
                if ($idadi === '' || $idadi === null) {
                    $idadi = 0;
                }
                
                // Validate bei nunua
                if ($beiNunua === '' || $beiNunua === null) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua inakosekana";
                    continue;
                }
                
                // Validate bei kuuza
                if ($beiKuuza === '' || $beiKuuza === null) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kuuza inakosekana";
                    continue;
                }
                
                // Validate numeric values
                if (!is_numeric($idadi) || (int)$idadi < 0) {
                    $errors[] = "Mstari {$lineNumber}: Idadi '{$idadi}' si sahihi";
                    continue;
                }
                
                if (!is_numeric($beiNunua) || (float)$beiNunua < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua '{$beiNunua}' si sahihi";
                    continue;
                }
                
                if (!is_numeric($beiKuuza) || (float)$beiKuuza < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kuuza '{$beiKuuza}' si sahihi";
                    continue;
                }
                
                // Validate price logic
                if ((float)$beiKuuza < (float)$beiNunua) {
                    $errors[] = "Mstari {$lineNumber}: Bei kuuza haiwezi kuwa chini ya bei nunua";
                    continue;
                }
                
                // Parse expiry date if provided
                $expiryDate = null;
                if (!empty($expiry) && strtolower($expiry) !== 'n/a' && strtolower($expiry) !== 'na') {
                    $parsedDate = $this->parseDate($expiry);
                    if (!$parsedDate) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$expiry}' si sahihi. Tumia format YYYY-MM-DD";
                        continue;
                    }
                    $expiryDate = $parsedDate;
                }
                
                // Check if product exists with same jina, aina, and kipimo
                $query = Bidhaa::where('company_id', $companyId)
                              ->where('jina', $jina)
                              ->where('aina', $aina);
                
                // Handle kipimo - if provided, match exactly; if not, match where kipimo is null or empty
                if (!empty($kipimo)) {
                    $query->where('kipimo', $kipimo);
                } else {
                    $query->where(function($q) {
                        $q->whereNull('kipimo')
                          ->orWhere('kipimo', '');
                    });
                }
                
                $existingProduct = $query->first();
                
                try {
                    if ($existingProduct) {
                        // UPDATE existing product - REPLACE quantity with Excel value (NOT ADD)
                        $updateData = [
                            'idadi' => (int)$idadi, // REPLACE, NOT ADD
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                        ];
                        
                        // Update expiry only if provided
                        if ($expiryDate) {
                            $updateData['expiry'] = $expiryDate;
                        }
                        
                        // Update barcode only if provided and not empty
                        if (!empty($barcode)) {
                            // Check if barcode already exists on another product
                            $barcodeExists = Bidhaa::where('barcode', $barcode)
                                                  ->where('company_id', $companyId)
                                                  ->where('id', '!=', $existingProduct->id)
                                                  ->exists();
                            
                            if (!$barcodeExists) {
                                $updateData['barcode'] = $barcode;
                            } else {
                                $errors[] = "Mstari {$lineNumber}: Barcode '{$barcode}' tayari ipo kwenye bidhaa nyingine";
                                continue;
                            }
                        }
                        
                        $existingProduct->update($updateData);
                        $updatedCount++;
                        
                    } else {
                        // CREATE new product
                        $createData = [
                            'company_id' => $companyId,
                            'jina' => $jina,
                            'aina' => $aina,
                            'kipimo' => !empty($kipimo) ? $kipimo : null,
                            'idadi' => (int)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                            'expiry' => $expiryDate,
                        ];
                        
                        // Add barcode if provided and not exists
                        if (!empty($barcode)) {
                            $barcodeExists = Bidhaa::where('barcode', $barcode)
                                                  ->where('company_id', $companyId)
                                                  ->exists();
                            
                            if (!$barcodeExists) {
                                $createData['barcode'] = $barcode;
                            } else {
                                $errors[] = "Mstari {$lineNumber}: Barcode '{$barcode}' tayari ipo";
                                continue;
                            }
                        }
                        
                        Bidhaa::create($createData);
                        $createdCount++;
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Mstari {$lineNumber}: Hitilafu ya ndani - " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            $response = [
                'success' => true,
                'message' => "Upakiaji umekamilika!",
                'data' => [
                    'successCount' => $successCount,
                    'errorCount' => count($errors),
                    'skippedRows' => $skippedRows,
                    'totalRows' => $totalRows,
                    'updatedCount' => $updatedCount,
                    'createdCount' => $createdCount,
                    'errors' => array_slice($errors, 0, 50)
                ]
            ];
            
            if (count($errors) > 0 && $successCount === 0) {
                $response['success'] = false;
                $response['message'] = "Upakiaji umeshindwa";
            } elseif (count($errors) > 0) {
                $response['message'] = "Upakiaji umekamilika kwa hitilafu " . count($errors);
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Excel upload error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika usindikaji: ' . $e->getMessage(),
                'data' => [
                    'successCount' => 0,
                    'errorCount' => 1,
                    'skippedRows' => 0,
                    'totalRows' => 0,
                    'updatedCount' => 0,
                    'createdCount' => 0,
                    'errors' => ['Hitilafu ya jumla: ' . $e->getMessage()]
                ]
            ], 500);
        }
    }

    /**
     * Helper to get value from Excel row with flexible matching
     */
    private function getExcelValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            $key = strtolower(trim($key));
            foreach ($row as $rowKey => $value) {
                $rowKeyLower = strtolower(trim($rowKey));
                if ($rowKeyLower == $key) {
                    return $value;
                }
            }
        }
        return '';
    }
    
    /**
     * Process Excel file (XLS, XLSX)
     */
    private function processExcelFile($file)
    {
        $rows = [];
        
        try {
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library haijapatikana');
            }
            
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Get headers from first row
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $cellValue = $worksheet->getCell($columnLetter . '1')->getValue();
                if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                    $cellValue = $cellValue->getPlainText();
                }
                $headers[$col] = trim($cellValue ?? '');
            }
            
            // Process data rows (starting from row 2)
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $cellValue = $worksheet->getCell($columnLetter . $row)->getValue();
                    
                    if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                        $cellValue = $cellValue->getPlainText();
                    }
                    
                    $header = $headers[$col] ?? 'Column' . $col;
                    if (!empty($header)) {
                        $rowData[$header] = trim($cellValue ?? '');
                    }
                }
                
                // Only add non-empty rows
                $hasData = false;
                foreach ($rowData as $value) {
                    if (!empty($value)) {
                        $hasData = true;
                        break;
                    }
                }
                
                if ($hasData) {
                    $rows[] = $rowData;
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Excel processing error: ' . $e->getMessage());
            throw new \Exception('Hitilafu katika kusoma faili la Excel: ' . $e->getMessage());
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
        
        $dateString = preg_split('/[\s,]+/', $dateString)[0];
        
        $formats = [
            'Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y',
            'Y/m/d', 'Y.m.d', 'd.m.Y', 'Ymd'
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date && $date->format($format) === $dateString) {
                return $date->format('Y-m-d');
            }
        }
        
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        return null;
    }
    
    /**
     * Barcode operations
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
}