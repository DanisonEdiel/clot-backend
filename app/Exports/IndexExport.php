<?php

namespace App\Exports;

use App\Models\Ruc;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class IndexExport implements WithEvents, WithTitle

{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $templatePath;
    private $ruc;
    private $month;
    private $year;

    public function __construct($tenantId, $ruc, $month, $year)
    {
        //  dd($vouchers);
        $this->templatePath = storage_path('templates/Indice.xlsx');
        $this->month = $month;
        $this->year = $year;
        $this->ruc = Ruc::where('tenant_id', $tenantId)->where('ruc', $ruc)->first();
    }

    public function registerEvents(): array
    {
        Carbon::setLocale('es');

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = IOFactory::load($this->templatePath);
                $worksheet = $spreadsheet->getActiveSheet();

                $worksheet->setCellValue('C5', $this->ruc->ruc);
                $worksheet->setCellValue('C6', $this->ruc->name);
                $worksheet->setCellValue('F3', now());
                $formattedDate = Carbon::createFromDate($this->year, $this->month)->isoFormat('MMMM') . ' - ' . $this->year;
                $worksheet->setCellValue('F4', ucfirst($formattedDate));

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(0);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
            },
        ];
    }
    public function title(): string
    {
        return 'Index';
    }
}
