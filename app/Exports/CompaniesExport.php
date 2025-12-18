<?php


namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompaniesExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle,
    WithColumnWidths
{
    protected $companies;
    protected $title;

    public function __construct($companies, $title)
    {
        $this->companies = $companies;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->companies;
    }

    public function headings(): array
    {
        return [
            'Jina la Kampuni',
            'Jina la Mmiliki',
            'Namba ya Simu',
            'Barua Pepe',
            'Mkoa',
            'Kifurushi',
            'Database',
            'Tarehe ya Usajili',
            'Imethibitishwa',
            'Mtumiaji Ameidhinishwa'
        ];
    }

    public function map($company): array
    {
        return [
            $company->company_name,
            $company->owner_name,
            $company->phone,
            $company->email,
            $company->region,
            $company->package ?? 'Hakuna',
            $company->database_name ?? 'Hakuna',
            $company->created_at->format('d/m/Y H:i'),
            $company->is_verified ? 'Ndio' : 'Hapana',
            $company->is_user_approved ? 'Ndio' : 'Hapana',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row bold
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 15,
            'D' => 25,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 15,
            'J' => 20,
        ];
    }

    public function title(): string
    {
        return 'Makampuni';
    }
}
