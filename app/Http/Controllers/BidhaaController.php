<?php
// app/Http/Controllers/BidhaaController.php

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
        if (Auth::guard('web')->check()) {
            return true;
        }

        $employee = Auth::guard('mfanyakazi')->user();

        if (!$employee) {
            return false;
        }

        $uwezo = strtolower(trim($employee->uwezo ?? ''));

        return in_array($uwezo, ['mkubwa']);
    }

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

        // Handle PDF Export
        if ($request->has('export') && $request->export === 'pdf') {
            $exportQuery = Bidhaa::where('company_id', $companyId);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $exportQuery->where(function($q) use ($search) {
                    $q->where('jina', 'LIKE', "%{$search}%")
                      ->orWhere('aina', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%")
                      ->orWhere('kipimo', 'LIKE', "%{$search}%");
                });
            }

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
                }
            }

            $productsForPdf = $exportQuery->orderBy('jina')->get();

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

        // Handle Excel Export
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportExcel($request);
        }

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
        ) + [
            'canViewPurchasePrice' => $this->canViewPurchasePrice(),
            'canEditProduct' => $this->canViewPurchasePrice()
        ]);
    }

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
                    'bei_uzo_jumla' => $item->bei_uzo_jumla,
                    'bei_kiasi_cha_chaguo' => $item->bei_kiasi_cha_chaguo,
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

    public function editProduct($id)
    {
        if (!$this->canViewPurchasePrice()) {
            return response()->json([
                'success' => false,
                'message' => 'Huna ruhusa ya kurekebisha bidhaa.'
            ], 403);
        }

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
                    'bei_uzo_jumla' => $product->bei_uzo_jumla,
                    'bei_kiasi_cha_chaguo' => $product->bei_kiasi_cha_chaguo,
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

 public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'jina' => 'required|string|max:255',
        'aina' => 'required|string|max:255',
        'kipimo' => 'nullable|string|max:100',
        'idadi' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        'bei_nunua' => 'required|numeric|min:0',
        'bei_kuuza' => 'required|numeric|min:0',
        'bei_uzo_jumla' => 'nullable|numeric|min:0',
        'bei_kiasi_cha_chaguo' => 'sometimes|in:rejareja,jumla',
        'expiry' => 'nullable|date',
        'barcode' => [
            'nullable',
            'string',
            'max:255',
            Rule::unique('bidhaas', 'barcode')->where(function ($query) {
                return $query->where('company_id', $this->getCompanyId());
            })
        ],
    ]);

    $validator->after(function ($validator) use ($request) {
        if ($request->bei_kuuza < $request->bei_nunua) {
            $validator->errors()->add('bei_kuuza', 'Bei ya kuuza (rejareja) haiwezi kuwa chini ya bei ya kununua');
        }
        
        if ($request->filled('bei_uzo_jumla') && $request->bei_uzo_jumla < $request->bei_nunua) {
            $validator->errors()->add('bei_uzo_jumla', 'Bei ya jumla haiwezi kuwa chini ya bei ya kununua');
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
    
    // Set default price type if not specified
    if (!isset($validated['bei_kiasi_cha_chaguo'])) {
        $validated['bei_kiasi_cha_chaguo'] = 'rejareja';
    }
    
    // IMPORTANT: Only set to null if empty or zero, otherwise use the value
    $validated['bei_uzo_jumla'] = !empty($validated['bei_uzo_jumla']) && $validated['bei_uzo_jumla'] > 0 
        ? $validated['bei_uzo_jumla'] 
        : null;

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
    public function update(Request $request, $id)
    {
        if (!$this->canViewPurchasePrice()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Huna ruhusa ya kurekebisha bidhaa.'
                ], 403);
            }
            return redirect()->route('bidhaa.index')
                ->with('error', 'Huna ruhusa ya kurekebisha bidhaa.');
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
            'bei_uzo_jumla' => 'nullable|numeric|min:0',
            'bei_kiasi_cha_chaguo' => 'sometimes|in:rejareja,jumla',
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
                $validator->errors()->add('bei_kuuza', 'Bei ya kuuza (rejareja) haiwezi kuwa chini ya bei ya kununua');
            }
            
            if ($request->filled('bei_uzo_jumla') && $request->bei_uzo_jumla < $request->bei_nunua) {
                $validator->errors()->add('bei_uzo_jumla', 'Bei ya jumla haiwezi kuwa chini ya bei ya kununua');
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
        
        if (empty($validated['bei_uzo_jumla'])) {
            $validated['bei_uzo_jumla'] = null;
        }
        
        if (!isset($validated['bei_kiasi_cha_chaguo'])) {
            $validated['bei_kiasi_cha_chaguo'] = $bidhaa->bei_kiasi_cha_chaguo ?? 'rejareja';
        }
        
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

    public function destroy($id, Request $request)
    {
        $companyId = $this->getCompanyId();
        $bidhaa = Bidhaa::where('id', $id)
                        ->where('company_id', $companyId)
                        ->firstOrFail();

        $isAllowed = $this->isBoss() || $this->canViewPurchasePrice();

        if (!$isAllowed) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Huna ruhusa ya kufuta bidhaa.'
                ], 403);
            }
            return redirect()->route('bidhaa.index')
                ->with('error', 'Huna ruhusa ya kufuta bidhaa.');
        }

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
 * Delete all products
 */
public function deleteAll(Request $request)
{
    $companyId = $this->getCompanyId();
    
    // Check permission: only Boss can delete all
    if (!$this->isBoss()) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Huna ruhusa ya kufuta bidhaa zote.'
            ], 403);
        }
        return redirect()->route('bidhaa.index')->with('error', 'Huna ruhusa ya kufuta bidhaa zote.');
    }
    
    try {
        // Get count before deletion - FIXED QUERY
        $count = Bidhaa::where('company_id', $companyId)->count();
        
        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna bidhaa za kufuta'
            ]);
        }
        
        // Delete all products - FIXED QUERY
        $deleted = Bidhaa::where('company_id', $companyId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Bidhaa zote ({$count}) zimefutwa kikamilifu!"
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Hitilafu: ' . $e->getMessage()
        ], 500);
    }
}
    public function exportExcel(Request $request)
    {
        $companyId = $this->getCompanyId();
        $company = $this->getCurrentUser()->company->name ?? 'Kampuni';
        
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

        $products = $query->orderBy('jina')->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Bidhaa Zote');
        
        $headers = [
            'A1' => 'Jina la Bidhaa',
            'B1' => 'Aina',
            'C1' => 'Kipimo',
            'D1' => 'Idadi',
            'E1' => 'Bei Nunua (TZS)',
            'F1' => 'Bei Rejareja (TZS)',
            'G1' => 'Bei Jumla (TZS)',
            'H1' => 'Aina ya Bei Chaguo',
            'I1' => 'Expiry Date',
            'J1' => 'Barcode',
            'K1' => 'Faida (Rejareja)',
            'L1' => 'Faida (Jumla)',
            'M1' => 'Hali ya Hisa'
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
        
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        
        $sheet->getStyle('D2:D' . ($products->count() + 1))
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        
        $sheet->getStyle('E2:H' . ($products->count() + 1))
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        
        $row = 2;
        foreach ($products as $product) {
            $faidaRejareja = $product->bei_kuuza - $product->bei_nunua;
            $faidaJumla = $product->bei_uzo_jumla ? $product->bei_uzo_jumla - $product->bei_nunua : 0;
            
            $hali = 'Inapatikana';
            if ($product->idadi == 0) {
                $hali = 'Imeisha';
            } elseif ($product->idadi < 10) {
                $hali = 'Inakaribia kuisha';
            }
            
            $priceTypeLabel = $product->bei_kiasi_cha_chaguo === 'jumla' ? 'Jumla' : 'Rejareja';
            
            $sheet->setCellValue('A' . $row, $product->jina);
            $sheet->setCellValue('B' . $row, $product->aina);
            $sheet->setCellValue('C' . $row, $product->kipimo ?? '');
            $sheet->setCellValue('D' . $row, $product->idadi);
            $sheet->setCellValue('E' . $row, $product->bei_nunua);
            $sheet->setCellValue('F' . $row, $product->bei_kuuza);
            $sheet->setCellValue('G' . $row, $product->bei_uzo_jumla ?? '');
            $sheet->setCellValue('H' . $row, $priceTypeLabel);
            $sheet->setCellValue('I' . $row, $product->expiry ? \Carbon\Carbon::parse($product->expiry)->format('Y-m-d') : '');
            $sheet->setCellValue('J' . $row, $product->barcode ?? '');
            $sheet->setCellValue('K' . $row, $faidaRejareja . ' TZS');
            $sheet->setCellValue('L' . $row, ($product->bei_uzo_jumla ? $faidaJumla . ' TZS' : '-'));
            $sheet->setCellValue('M' . $row, $hali);
            
            $row++;
        }
        
        foreach (range('A', 'M') as $col) {
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
            'E1' => 'Bei Nunua (TZS)',
            'F1' => 'Bei Rejareja (TZS)',
            'G1' => 'Bei Jumla (TZS)',
            'H1' => 'Expiry Date',
            'I1' => 'Barcode'
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
        
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        $sheet->getStyle('D2:D3')
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        
        $sheet->getStyle('E2:G3')
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        
        $sampleData = [
            ['Soda', 'Vinywaji', '500ml', 100, 600, 1000, 800, '2025-12-31', '123456789'],
            ['Unga', 'Chakula', '2kg', 50.5, 2500, 3500, 3000, '2026-06-30', ''],
            ['Maziwa', 'Vinywaji', '1L', 75, 1800, 2200, 1900, '2025-10-15', '987654321']
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
            $sheet->setCellValue('I' . $row, $data[8]);
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
        $sheet->getStyle('A2:I' . $lastRow)->applyFromArray($dataStyle);
        
        foreach (range('A', 'I') as $col) {
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
        $updatedCount = 0;
        $createdCount = 0;
        $skippedRows = 0;
        $totalRows = 0;

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
                $beiNunua = $this->getExcelValue($rowData, ['Bei Nunua (TZS)', 'Bei Nunua', 'bei nunua', 'Bei', 'bei']);
                $beiRejareja = $this->getExcelValue($rowData, ['Bei Rejareja (TZS)', 'Bei Kuuza', 'bei kuuza', 'Bei Rejareja']);
                $beiJumla = $this->getExcelValue($rowData, ['Bei Jumla (TZS)', 'Bei Jumla', 'bei jumla']);
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
                
                if ($beiRejareja === '' || $beiRejareja === null) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya rejareja inakosekana";
                    continue;
                }
                
                if (!is_numeric($idadi) || (float)$idadi < 0) {
                    $errors[] = "Mstari {$lineNumber}: Idadi '{$idadi}' si sahihi";
                    continue;
                }
                
                if (!is_numeric($beiNunua) || (float)$beiNunua < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya kununua '{$beiNunua}' si sahihi";
                    continue;
                }
                
                if (!is_numeric($beiRejareja) || (float)$beiRejareja < 0) {
                    $errors[] = "Mstari {$lineNumber}: Bei ya rejareja '{$beiRejareja}' si sahihi";
                    continue;
                }
                
                if ((float)$beiRejareja < (float)$beiNunua) {
                    $errors[] = "Mstari {$lineNumber}: Bei rejareja haiwezi kuwa chini ya bei nunua";
                    continue;
                }
                
                $beiJumlaValue = null;
                if (!empty($beiJumla) && is_numeric($beiJumla)) {
                    if ((float)$beiJumla < (float)$beiNunua) {
                        $errors[] = "Mstari {$lineNumber}: Bei jumla haiwezi kuwa chini ya bei nunua";
                        continue;
                    }
                    if ((float)$beiJumla >= (float)$beiRejareja) {
                        $errors[] = "Mstari {$lineNumber}: Bei jumla inapaswa kuwa chini ya bei rejareja";
                        continue;
                    }
                    $beiJumlaValue = (float)$beiJumla;
                }
                
                $expiryDate = null;
                if (!empty($expiry) && strtolower($expiry) !== 'n/a' && strtolower($expiry) !== 'na') {
                    $parsedDate = $this->parseDate($expiry);
                    if (!$parsedDate) {
                        $errors[] = "Mstari {$lineNumber}: Tarehe ya expiry '{$expiry}' si sahihi";
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
                            'bei_kuuza' => (float)$beiRejareja,
                            'bei_uzo_jumla' => $beiJumlaValue,
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
                                $errors[] = "Mstari {$lineNumber}: Barcode '{$barcode}' tayari ipo";
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
                            'bei_kuuza' => (float)$beiRejareja,
                            'bei_uzo_jumla' => $beiJumlaValue,
                            'bei_kiasi_cha_chaguo' => 'rejareja',
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
                    $errors[] = "Mstari {$lineNumber}: Hitilafu - " . $e->getMessage();
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
            \Log::error('Excel upload error: ' . $e->getMessage());
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
                    'errors' => ['Hitilafu: ' . $e->getMessage()]
                ]
            ], 500);
        }
    }

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
            throw new \Exception('Hitilafu katika kusoma faili: ' . $e->getMessage());
        }
        
        return $rows;
    }
    
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

        $currentStock = (float)$bidhaa->idadi;
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $totalIngizo = 0;
        $totalMauzoCash = 0;
        $totalMauzoCredit = 0;
        $totalMarejeshoAmount = 0;
        $histories = [];

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

        if (class_exists('App\Models\Mauzo')) {
            $mauzoQuery = Mauzo::where('bidhaa_id', $bidhaa->id)
                ->where('company_id', $companyId);
            
            if ($startDate && $endDate) {
                $mauzoQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            
            $mauzo = $mauzoQuery->orderBy('created_at', 'asc')->get();
            
            foreach ($mauzo as $m) {
                $isCredit = !empty($m->madeni_id);
                
                if ($isCredit) {
                    $totalMauzoCredit += $m->idadi;
                    $histories[] = [
                        'tarehe' => $m->created_at->format('d/m/Y H:i'),
                        'aina' => 'kopesha',
                        'idadi_iliyoingizwa' => 0,
                        'idadi_iliyouzwa' => (float)$m->idadi,
                        'kiasi_cha_fedha' => (float)$m->jumla,
                        'maelezo' => 'Kopesha: ' . number_format($m->jumla, 0) . ' TZS',
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
                        'maelezo' => 'Mauzo: ' . number_format($m->jumla, 0) . ' TZS',
                        'timestamp' => $m->created_at->timestamp,
                        'unique_id' => 'mauzo_' . $m->id
                    ];
                }
            }
        }

        $uniqueHistories = [];
        foreach ($histories as $history) {
            $uniqueHistories[$history['unique_id']] = $history;
        }
        $histories = array_values($uniqueHistories);

        usort($histories, function($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
        });

        $runningBalance = 0;
        $balances = [];
        
        foreach ($histories as $index => $history) {
            if ($history['aina'] == 'manunuzi') {
                $runningBalance += $history['idadi_iliyoingizwa'];
            } elseif (in_array($history['aina'], ['mauzo', 'kopesha'])) {
                $runningBalance -= $history['idadi_iliyouzwa'];
            }
            $balances[$index] = $runningBalance;
        }

        $finalCalculatedBalance = $runningBalance;
        $adjustmentFactor = $currentStock - $finalCalculatedBalance;
        
        $historiesWithBalance = [];
        foreach ($histories as $index => $history) {
            $adjustedBalance = $balances[$index] + $adjustmentFactor;
            $history['idadi_iliyobaki'] = $adjustedBalance;
            $historiesWithBalance[] = $history;
        }

        usort($historiesWithBalance, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        $histories = $historiesWithBalance;
        $totalMauzoJumla = $totalMauzoCash + $totalMauzoCredit;

        $data = [
            'bidhaa' => [
                'id' => $bidhaa->id,
                'jina' => $bidhaa->jina,
                'aina' => $bidhaa->aina,
                'kipimo' => $bidhaa->kipimo,
                'barcode' => $bidhaa->barcode,
                'idadi_sasa' => $currentStock,
                'idadi_format' => number_format($currentStock, 2),
                'bei_kuuza' => $bidhaa->bei_kuuza,
                'bei_uzo_jumla' => $bidhaa->bei_uzo_jumla,
                'bei_kiasi_cha_chaguo' => $bidhaa->bei_kiasi_cha_chaguo,
                'expiry' => $bidhaa->expiry ? $bidhaa->expiry->format('Y-m-d') : null,
                'imeundwa' => $bidhaa->created_at->format('d/m/Y H:i'),
            ],
            'statistics' => [
                'tarehe_ya_kwanza' => $bidhaa->created_at->format('d/m/Y'),
                'jumlah_iliyoingizwa' => $totalIngizo,
                'jumlah_mauzo_cash' => $totalMauzoCash,
                'jumlah_kopesha' => $totalMauzoCredit,
                'jumlah_mauzo_jumla' => $totalMauzoJumla,
            ],
            'histories' => $histories,
            'total_transactions' => count($histories),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function searchProducts(Request $request)
    {
        $companyId = $this->getCompanyId();
        $search = $request->get('q', '');
        $id = $request->get('id', null);
        
        $query = Bidhaa::where('company_id', $companyId);
        
        if ($id) {
            $query->where('id', $id);
        }
        else if (strlen($search) >= 1) {
            $query->where(function($q) use ($search) {
                $q->where('jina', 'LIKE', "%{$search}%")
                  ->orWhere('aina', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        } else {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
        
        $products = $query->orderBy('jina')->get(['id', 'jina', 'aina', 'barcode', 'idadi', 'bei_kuuza', 'bei_uzo_jumla', 'bei_kiasi_cha_chaguo']);
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}