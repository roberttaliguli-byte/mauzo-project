<?php

namespace App\Http\Controllers;

use App\Models\Bidhaa;
use App\Models\Manunuzi;
use App\Models\Mauzo;
use App\Models\Madeni;
use App\Models\Marejesho;
use Illuminate\Http\Request;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class BidhaaController extends Controller
{
    private function getCurrentUser()
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            $user = Auth::guard('mfanyakazi')->user();
        }
        return $user;
    }
    
    private function getCompanyId()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            abort(403, 'Unauthorized - Please login');
        }
        return $user->company_id;
    }
    
    private function isBoss()
    {
        return Auth::guard('web')->check();
    }


private function canViewPurchasePrice()
{
    // Boss via web guard
    if (Auth::guard('web')->check()) {
        return true;
    }
    
    // Employee via mfanyakazi guard
    $employee = Auth::guard('mfanyakazi')->user();
    if ($employee && method_exists($employee, 'hasFullAccess')) {
        return $employee->hasFullAccess(); // uses uwezo === 'mkubwa'
    }
    
    return false;
}
    
/**
 * Display a listing of the products with pagination
 */
public function index(Request $request)
{
    $companyId = $this->getCompanyId();
    $perPage = $request->input('per_page', 10);
    $isBoss = $this->isBoss();

    $query = Bidhaa::where('company_id', $companyId);

    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('jina', 'LIKE', "%{$search}%")
              ->orWhere('aina', 'LIKE', "%{$search}%")
              ->orWhere('barcode', 'LIKE', "%{$search}%")
              ->orWhere('kipimo', 'LIKE', "%{$search}%");
        });
    }

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

    // Handle PDF Export - GET ALL PRODUCTS (no pagination)
    if ($request->has('export') && $request->export === 'pdf') {
        // Clone the query to get ALL products based on filters (but no pagination)
        $exportQuery = Bidhaa::where('company_id', $companyId);

        // Apply search if present
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $exportQuery->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('aina', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%")
                  ->orWhere('kipimo', 'LIKE', "%{$search}%");
            });
        }

        // Apply filter if present
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'available':
                    $exportQuery->where('idadi', '>', 0);
                    break;
                case 'low_stock':
                    $exportQuery->where('idadi', '<', 10)->where('idadi', '>', 0);
                    break;
                case 'expired':
                    $exportQuery->where('expiry', '<', now());
                    break;
                case 'out_of_stock':
                    $exportQuery->where('idadi', 0);
                    break;
                // No filter = all products including out of stock
            }
        }

        // GET ALL - no pagination
        $productsForPdf = $exportQuery->orderBy('jina')->get();

        // Calculate statistics
        $outOfStockCount = $productsForPdf->where('idadi', 0)->count();
        $inStockCount = $productsForPdf->where('idadi', '>', 0)->count();
        $lowStockCount = $productsForPdf->where('idadi', '<', 10)->where('idadi', '>', 0)->count();
        $expiredCount = $productsForPdf->filter(function($product) {
            if (!$product->expiry) return false;
            return \Carbon\Carbon::parse($product->expiry) < now();
        })->count();

        $data = [
            'bidhaa' => $productsForPdf,
            'title' => 'Orodha ya Bidhaa',
            'date' => now()->format('d/m/Y'),
            'company' => $this->getCurrentUser()->company,
            'total_count' => $productsForPdf->count(),
            'outOfStockCount' => $outOfStockCount,
            'inStockCount' => $inStockCount,
            'lowStockCount' => $lowStockCount,
            'expiredCount' => $expiredCount,
            'filter' => $request->filter,
            'search' => $request->search
        ];

        $pdf = Pdf::loadView('bidhaa.pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = "bidhaa_zote_" . date('Y-m-d') . ".pdf";
        if ($request->filter) {
            $filename = "bidhaa_" . $request->filter . "_" . date('Y-m-d') . ".pdf";
        }
        if ($request->search) {
            $filename = "bidhaa_search_" . date('Y-m-d') . ".pdf";
        }

        return $pdf->download($filename);
    }

    // Handle Excel Export - Pass to dedicated method
    if ($request->has('export') && $request->export === 'excel') {
        return $this->exportExcel($request);
    }

    // Regular paginated results for normal view
    $bidhaa = $query->orderBy('created_at', 'desc')
                   ->paginate($perPage)
                   ->appends($request->except('page'));

    $totalProducts = Bidhaa::where('company_id', $companyId)->count();
    $availableProducts = Bidhaa::where('company_id', $companyId)->where('idadi', '>', 0)->count();
    $lowStockProducts = Bidhaa::where('company_id', $companyId)->where('idadi', '<', 10)->where('idadi', '>', 0)->count();
    $expiredProducts = Bidhaa::where('company_id', $companyId)->where('expiry', '<', now())->count();
    $outOfStockProducts = Bidhaa::where('company_id', $companyId)->where('idadi', 0)->count();

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
) + ['canViewPurchasePrice' => $this->canViewPurchasePrice()]);
}

    /**
     * Search all products (returns ALL results, no pagination)
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
            // Get ALL products matching search (no pagination)
            $bidhaa = Bidhaa::where('company_id', $companyId)
                ->where(function($query) use ($search) {
                    $query->where('jina', 'LIKE', "%{$search}%")
                          ->orWhere('aina', 'LIKE', "%{$search}%")
                          ->orWhere('barcode', 'LIKE', "%{$search}%")
                          ->orWhere('kipimo', 'LIKE', "%{$search}%");
                })
                ->orderBy('jina')
                ->get();
            
            $mappedResults = $bidhaa->map(function($item) {
                $stockStatus = 'in-stock';
                if ($item->idadi == 0) {
                    $stockStatus = 'out-of-stock';
                } elseif ($item->idadi < 10) {
                    $stockStatus = 'low-stock';
                }
                
                $expiryDate = null;
                if ($item->expiry) {
                    if ($item->expiry instanceof \Carbon\Carbon) {
                        $expiryDate = $item->expiry->format('Y-m-d');
                    } else {
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
     * Get product for editing
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
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jina' => 'required|string|max:255',
            'aina' => 'required|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
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
            'idadi.numeric' => 'Idadi lazima iwe namba',
            'idadi.min' => 'Idadi haiwezi kuwa chini ya 0',
            'idadi.regex' => 'Idadi inaweza kuwa na sehemu ya desimali hadi nafasi 2 (mfano: 1.5, 2.75)',
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
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
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
            'idadi' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
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
     * Remove the specified product
     */
    public function destroy($id, Request $request)
    {
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
 * Export all products to Excel (including out of stock)
 */
public function exportExcel(Request $request)
{
    $companyId = $this->getCompanyId();
    $company = $this->getCurrentUser()->company->name ?? 'Kampuni';
    
    // Get ALL products based on filters (but no pagination)
    $query = Bidhaa::where('company_id', $companyId);

    // Apply search if present
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('jina', 'LIKE', "%{$search}%")
              ->orWhere('aina', 'LIKE', "%{$search}%")
              ->orWhere('barcode', 'LIKE', "%{$search}%")
              ->orWhere('kipimo', 'LIKE', "%{$search}%");
        });
    }

    // Apply filter if present
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
            // No filter = all products including out of stock
        }
    }

    // GET ALL - no pagination
    $products = $query->orderBy('jina')->get();
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setTitle('Bidhaa Zote');
    
    $headers = [
        'A1' => 'Jina la Bidhaa',
        'B1' => 'Aina',
        'C1' => 'Kipimo',
        'D1' => 'Idadi',
        'E1' => 'Bei Nunua',
        'F1' => 'Bei Kuuza',
        'G1' => 'Expiry Date',
        'H1' => 'Barcode',
        'I1' => 'Faida',
        'J1' => 'Asilimia',
        'K1' => 'Hali ya Hisa'
    ];
    
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
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
    
    $sheet->getStyle('D2:D' . ($products->count() + 1))
          ->getNumberFormat()
          ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    
    $sheet->getStyle('E2:G' . ($products->count() + 1))
          ->getNumberFormat()
          ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    
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
    
    foreach (range('A', 'K') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $writer = new Xlsx($spreadsheet);
    
    $filename = "bidhaa_zote_" . date('Y-m-d_His') . ".xlsx";
    if ($request->filter) {
        $filename = "bidhaa_" . $request->filter . "_" . date('Y-m-d') . ".xlsx";
    }
    if ($request->search) {
        $filename = "bidhaa_search_" . date('Y-m-d') . ".xlsx";
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}
    /**
     * Download sample Excel file
     */
    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Sampuli');
        
        $headers = [
            'A1' => 'Jina la Bidhaa',
            'B1' => 'Aina',
            'C1' => 'Kipimo',
            'D1' => 'Idadi',
            'E1' => 'Bei Nunua',
            'F1' => 'Bei Kuuza',
            'G1' => 'Expiry Date',
            'H1' => 'Barcode'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
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
        
        $sheet->getStyle('D2:D3')
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        
        $sheet->getStyle('E2:F3')
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        
        $sampleData = [
            ['Soda', 'Vinywaji', '500ml', 100, 600, 1000, '2025-12-31', '123456789'],
            ['Unga', 'Chakula', '2kg', 50.5, 2500, 3500, '2026-06-30', '']
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
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = "sampuli_bidhaa_" . date('Y-m-d') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Upload Excel file and process products
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
                $lineNumber = $index + 2;
                
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
                
                $jina = $this->getExcelValue($rowData, ['Jina la Bidhaa', 'jina la bidhaa', 'Jina', 'jina']);
                $aina = $this->getExcelValue($rowData, ['Aina', 'aina']);
                $kipimo = $this->getExcelValue($rowData, ['Kipimo', 'kipimo']);
                $idadi = $this->getExcelValue($rowData, ['Idadi', 'idadi']);
                $beiNunua = $this->getExcelValue($rowData, ['Bei Nunua', 'bei nunua', 'Bei', 'bei']);
                $beiKuuza = $this->getExcelValue($rowData, ['Bei Kuuza', 'bei kuuza']);
                $expiry = $this->getExcelValue($rowData, ['Expiry Date', 'expiry date', 'Expiry', 'expiry']);
                $barcode = $this->getExcelValue($rowData, ['Barcode', 'barcode']);
                
                if (empty($jina)) {
                    $errors[] = "Mstari {$lineNumber}: Jina la bidhaa linakosekana";
                    continue;
                }
                
                if (empty($aina)) {
                    $errors[] = "Mstari {$lineNumber}: Aina ya bidhaa inakosekana";
                    continue;
                }
                
                if ($idadi === '' || $idadi === null) {
                    $idadi = 0;
                }
                
                if ($beiNunua === '' || $beiNunua === null) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua inakosekana";
                    continue;
                }
                
                if ($beiKuuza === '' || $beiKuuza === null) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kuuza inakosekana";
                    continue;
                }
                
                if (!is_numeric($idadi) || (float)$idadi < 0) {
                    $errors[] = "Mstari {$lineNumber}: Idadi '{$idadi}' si sahihi. Tumia namba (mfano: 1, 1.5, 2.75)";
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
                
                if ((float)$beiKuuza < (float)$beiNunua) {
                    $errors[] = "Mstari {$lineNumber}: Bei kuuza haiwezi kuwa chini ya bei nunua";
                    continue;
                }
                
                $expiryDate = null;
                if (!empty($expiry) && strtolower($expiry) !== 'n/a' && strtolower($expiry) !== 'na') {
                    $parsedDate = $this->parseDate($expiry);
                    if (!$parsedDate) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$expiry}' si sahihi. Tumia format YYYY-MM-DD";
                        continue;
                    }
                    $expiryDate = $parsedDate;
                }
                
                $query = Bidhaa::where('company_id', $companyId)
                              ->where('jina', $jina)
                              ->where('aina', $aina);
                
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
                        $updateData = [
                            'idadi' => (float)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                        ];
                        
                        if ($expiryDate) {
                            $updateData['expiry'] = $expiryDate;
                        }
                        
                        if (!empty($barcode)) {
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
                        $createData = [
                            'company_id' => $companyId,
                            'jina' => $jina,
                            'aina' => $aina,
                            'kipimo' => !empty($kipimo) ? $kipimo : null,
                            'idadi' => (float)$idadi,
                            'bei_nunua' => (float)$beiNunua,
                            'bei_kuuza' => (float)$beiKuuza,
                            'expiry' => $expiryDate,
                        ];
                        
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
     * Get value from Excel row by possible keys
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
     * Process Excel file and extract rows
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
            
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $cellValue = $worksheet->getCell($columnLetter . '1')->getValue();
                if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                    $cellValue = $cellValue->getPlainText();
                }
                $headers[$col] = trim($cellValue ?? '');
            }
            
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
     * Parse date string to Y-m-d format
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
     * Store product with barcode
     */
    public function storeBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string|max:255',
            'jina' => 'required|string|max:255',
            'aina' => 'nullable|string|max:255',
            'kipimo' => 'nullable|string|max:100',
            'idadi' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
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

    /**
     * Search product by barcode
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
 * Get detailed information for a specific product with stock management
 */
public function taarifa(Request $request)
{
    $companyId = $this->getCompanyId();
    
    $validator = Validator::make($request->all(), [
        'bidhaa_id' => 'required|exists:bidhaas,id,company_id,' . $companyId,
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Bidhaa haijachaguliwa',
            'errors' => $validator->errors()
        ], 422);
    }

    $bidhaa = Bidhaa::where('id', $request->bidhaa_id)
        ->where('company_id', $companyId)
        ->first();

    if (!$bidhaa) {
        return response()->json([
            'success' => false,
            'message' => 'Bidhaa haijapatikana'
        ], 404);
    }

    // Get current stock
    $currentStock = (float)$bidhaa->idadi;

    // Get date range filters
    $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
    $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

    // Initialize counters
    $totalIngizo = 0;
    $totalMauzoCash = 0;
    $totalMauzoCredit = 0;
    $totalMarejeshoAmount = 0;
    $histories = [];

    // 1. Get purchases (manunuzi) - ADDS to stock
    if (class_exists('App\Models\Manunuzi')) {
        $manunuziQuery = Manunuzi::where('bidhaa_id', $bidhaa->id)
            ->where('company_id', $companyId);
        
        if ($startDate && $endDate) {
            $manunuziQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $manunuzi = $manunuziQuery->orderBy('created_at', 'asc')->get();
        
        foreach ($manunuzi as $m) {
            $totalIngizo += $m->idadi;
            $histories[] = [
                'tarehe' => $m->created_at->format('d/m/Y H:i'),
                'aina' => 'manunuzi',
                'idadi_iliyoingizwa' => (float)$m->idadi,
                'idadi_iliyouzwa' => 0,
                'kiasi_cha_fedha' => 0,
                'maelezo' => $m->maelezo ?? 'Manunuzi',
                'timestamp' => $m->created_at->timestamp,
                'unique_id' => 'manunuzi_' . $m->id
            ];
        }
    }

    // 2. Get ALL sales (mauzo) from Mauzo table - SUBTRACTS from stock
    if (class_exists('App\Models\Mauzo')) {
        $mauzoQuery = Mauzo::where('bidhaa_id', $bidhaa->id)
            ->where('company_id', $companyId);
        
        if ($startDate && $endDate) {
            $mauzoQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $mauzo = $mauzoQuery->orderBy('created_at', 'asc')->get();
        
        foreach ($mauzo as $m) {
            // Check if this is a credit sale (has madeni_id)
            $isCredit = !empty($m->madeni_id);
            
            if ($isCredit) {
                $totalMauzoCredit += $m->idadi;
                $histories[] = [
                    'tarehe' => $m->created_at->format('d/m/Y H:i'),
                    'aina' => 'kopesha',
                    'idadi_iliyoingizwa' => 0,
                    'idadi_iliyouzwa' => (float)$m->idadi,
                    'kiasi_cha_fedha' => (float)$m->jumla,
                    'maelezo' => 'Kopesha - ' . ($m->lipa_kwa ?? 'Malipo') . ': ' . number_format($m->jumla, 0) . ' TZS',
                    'timestamp' => $m->created_at->timestamp,
                    'unique_id' => 'kopesha_' . $m->id
                ];
            } else {
                $totalMauzoCash += $m->idadi;
                $histories[] = [
                    'tarehe' => $m->created_at->format('d/m/Y H:i'),
                    'aina' => 'mauzo',
                    'idadi_iliyoingizwa' => 0,
                    'idadi_iliyouzwa' => (float)$m->idadi,
                    'kiasi_cha_fedha' => (float)$m->jumla,
                    'maelezo' => 'Mauzo - ' . ($m->lipa_kwa ?? 'Cash') . ': ' . number_format($m->jumla, 0) . ' TZS',
                    'timestamp' => $m->created_at->timestamp,
                    'unique_id' => 'mauzo_' . $m->id
                ];
            }
        }
    }

    // 3. ALSO get credit sales from Madeni table (in case they're not in Mauzo)
    if (class_exists('App\Models\Madeni')) {
        $madeniQuery = Madeni::where('bidhaa_id', $bidhaa->id)
            ->where('company_id', $companyId);
        
        if ($startDate && $endDate) {
            $madeniQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $madeni = $madeniQuery->orderBy('created_at', 'asc')->get();
        
        foreach ($madeni as $m) {
            // Check if this credit sale is already recorded in histories
            $exists = false;
            foreach ($histories as $h) {
                if ($h['aina'] == 'kopesha' && 
                    abs($h['idadi_iliyouzwa'] - $m->idadi) < 0.01 && 
                    abs($h['timestamp'] - $m->created_at->timestamp) < 60) { // Within 1 minute
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $totalMauzoCredit += $m->idadi;
                $histories[] = [
                    'tarehe' => $m->created_at->format('d/m/Y H:i'),
                    'aina' => 'kopesha',
                    'idadi_iliyoingizwa' => 0,
                    'idadi_iliyouzwa' => (float)$m->idadi,
                    'kiasi_cha_fedha' => (float)$m->jumla,
                    'maelezo' => 'Kopesha - ' . ($m->jina_mkopaji ?? 'Mteja') . ', Deni: ' . number_format($m->jumla, 0) . ' TZS',
                    'timestamp' => $m->created_at->timestamp,
                    'unique_id' => 'madeni_' . $m->id
                ];
            }
        }
    }

    // 4. Get returns/payments (marejesho) - payments ONLY, no stock impact
    if (class_exists('App\Models\Marejesho')) {
        // Get through madeni relationship
        $madeniIds = Madeni::where('bidhaa_id', $bidhaa->id)
            ->where('company_id', $companyId)
            ->pluck('id');
        
        if ($madeniIds->isNotEmpty()) {
            $marejeshoQuery = Marejesho::whereIn('madeni_id', $madeniIds)
                ->where('company_id', $companyId);
            
            if ($startDate && $endDate) {
                $marejeshoQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            
            $marejesho = $marejeshoQuery->orderBy('created_at', 'asc')->get();
            
            foreach ($marejesho as $m) {
                $totalMarejeshoAmount += $m->kiasi;
                
                // Marejesho are payments - they DON'T affect stock
                $histories[] = [
                    'tarehe' => $m->created_at->format('d/m/Y H:i'),
                    'aina' => 'marejesho',
                    'idadi_iliyoingizwa' => 0, // No stock added
                    'idadi_iliyouzwa' => 0,     // No stock removed
                    'kiasi_cha_fedha' => (float)$m->kiasi,
                    'maelezo' => 'Marejesho ya deni - ' . ($m->lipa_kwa ?? 'Malipo') . ': ' . number_format($m->kiasi, 0) . ' TZS',
                    'timestamp' => $m->created_at->timestamp,
                    'unique_id' => 'marejesho_' . $m->id
                ];
            }
        }
    }

    // 5. Remove duplicates by unique_id
    $uniqueHistories = [];
    foreach ($histories as $history) {
        $uniqueHistories[$history['unique_id']] = $history;
    }
    $histories = array_values($uniqueHistories);

    // 6. Sort histories by timestamp (OLDEST first for calculation)
    usort($histories, function($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    // 7. Calculate running balance FORWARD from zero
    $runningBalance = 0;
    $balances = [];
    
    foreach ($histories as $index => $history) {
        if ($history['aina'] == 'manunuzi') {
            $runningBalance += $history['idadi_iliyoingizwa'];
        } elseif (in_array($history['aina'], ['mauzo', 'kopesha'])) {
            $runningBalance -= $history['idadi_iliyouzwa'];
        }
        // Marejesho does NOT affect stock balance
        
        $balances[$index] = $runningBalance;
    }

    // 8. Calculate the adjustment factor to make final balance match current stock
    // Final running balance should equal current stock
    $finalCalculatedBalance = $runningBalance;
    $adjustmentFactor = $currentStock - $finalCalculatedBalance;
    
    // 9. Apply adjustment to all balances to make them relative to current stock
    $historiesWithBalance = [];
    foreach ($histories as $index => $history) {
        // Adjust the balance to show stock after each transaction
        // This makes the last transaction show current stock
        $adjustedBalance = $balances[$index] + $adjustmentFactor;
        
        $history['idadi_iliyobaki'] = $adjustedBalance;
        $historiesWithBalance[] = $history;
    }

    // 10. Sort back to NEWEST first for display
    usort($historiesWithBalance, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    $histories = $historiesWithBalance;

    // 11. Calculate totals correctly
    $totalMauzoJumla = $totalMauzoCash + $totalMauzoCredit; // All sales (cash + credit)
    $iliyobakiKwaHisabati = $totalIngizo - $totalMauzoJumla;
    $tofauti = $currentStock - $iliyobakiKwaHisabati;

    // 12. Prepare response data
    $data = [
        'bidhaa' => [
            'id' => $bidhaa->id,
            'jina' => $bidhaa->jina,
            'aina' => $bidhaa->aina,
            'kipimo' => $bidhaa->kipimo,
            'barcode' => $bidhaa->barcode,
            'idadi_sasa' => $currentStock,
            'idadi_format' => number_format($currentStock, 2),
            'expiry' => $bidhaa->expiry ? $bidhaa->expiry->format('Y-m-d') : null,
            'expiry_status' => $bidhaa->expiry ? (
                $bidhaa->expiry < now() ? 'expired' : 
                ($bidhaa->expiry <= now()->addDays(30) ? 'near_expiry' : 'good')
            ) : 'none',
            'imeundwa' => $bidhaa->created_at->format('d/m/Y H:i'),
        ],
        'statistics' => [
            'tarehe_ya_kwanza' => $bidhaa->created_at->format('d/m/Y'),
            'idadi_ya_kwanza' => $currentStock,
            'jumlah_iliyoingizwa' => $totalIngizo,
            'jumlah_mauzo_cash' => $totalMauzoCash,
            'jumlah_kopesha' => $totalMauzoCredit,
            'jumlah_mauzo_jumla' => $totalMauzoJumla, // This shows 5 (2+3)
            'jumlah_marejesho_fedha' => $totalMarejeshoAmount,
            'jumlah_iliyobaki_kwa_hisabati' => $iliyobakiKwaHisabati,
            'jumlah_iliyobaki_halisi' => $currentStock,
            'tofauti_ya_hisabati' => $tofauti,
        ],
        'histories' => $histories,
        'total_transactions' => count($histories),
        'date_range' => [
            'start' => $startDate ? $startDate->format('Y-m-d') : null,
            'end' => $endDate ? $endDate->format('Y-m-d') : null,
        ],
    ];

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

    /**
     * Search products for dropdown (returns ALL matching products, no pagination)
     */
    public function searchProducts(Request $request)
    {
        $companyId = $this->getCompanyId();
        $search = $request->get('q', '');
        $id = $request->get('id', null);
        
        $query = Bidhaa::where('company_id', $companyId);
        
        // If specific ID is requested
        if ($id) {
            $query->where('id', $id);
        }
        // Otherwise search by term
        else if (strlen($search) >= 1) {
            $query->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('aina', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        } else {
            // Return empty result if no search term
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
        
        // Get ALL matching products (no pagination, no limit)
        $products = $query->orderBy('jina')->get(['id', 'jina', 'aina', 'barcode', 'idadi']);
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Export product details as PDF
     */
    public function exportProductDetails($id)
    {
        $companyId = $this->getCompanyId();
        
        $bidhaa = Bidhaa::where('id', $id)
          ->where('company_id', $companyId)
          ->firstOrFail();
        
        // Get statistics
        $totalIngizo = 0;
        $totalMauzo = 0;
        
        if (class_exists('App\Models\Manunuzi')) {
            $totalIngizo = Manunuzi::where('bidhaa_id', $bidhaa->id)
                ->where('company_id', $companyId)
                ->sum('idadi');
        }
        
        if (class_exists('App\Models\Mauzo')) {
            $totalMauzo = Mauzo::where('bidhaa_id', $bidhaa->id)
                ->where('company_id', $companyId)
                ->sum('idadi');
        }
        
        // Calculate statistics
        $data = [
            'bidhaa' => $bidhaa,
            'totalIngizo' => $totalIngizo,
            'totalMauzo' => $totalMauzo,
            'statistics' => [
                'thamani_hisa' => $bidhaa->idadi * $bidhaa->bei_nunua,
                'thamani_mauzo' => $bidhaa->idadi * $bidhaa->bei_kuuza,
                'faida_tarajiwa' => $bidhaa->idadi * ($bidhaa->bei_kuuza - $bidhaa->bei_nunua),
            ],
            'company' => $this->getCurrentUser()->company,
            'date' => now()->format('d/m/Y H:i'),
        ];
        
        $pdf = Pdf::loadView('bidhaa.details-pdf', $data)
            ->setPaper('a4', 'portrait');
        
        return $pdf->download(
            'taarifa-' . Str::slug($bidhaa->jina) . '-' . date('Y-m-d') . '.pdf'
        );
    }
}