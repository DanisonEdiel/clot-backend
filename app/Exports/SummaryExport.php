<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class SummaryExport implements WithEvents, WithTitle

{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $templatePath;
    private $vouchers;
    private $month;
    private $year;
    private $downloadedVouchers;
    private $providers;
    private $sriVouchers;

    public function __construct($vouchers, $month, $year, $downloadedVouchers, $providers,$sriVouchers)
    {
        $this->templatePath = storage_path('templates/Resumen.xlsx');
        $this->month = $month;
        $this->year = $year;
        $this->vouchers = $vouchers;
        $this->downloadedVouchers = $downloadedVouchers;
        $this->providers = $providers;
        $this->sriVouchers = $sriVouchers;
    }

    public function registerEvents(): array
    {
        Carbon::setLocale('es');
        // dd($this->vouchers[1]['retentions']);
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = IOFactory::load($this->templatePath);
                $worksheet = $spreadsheet->getActiveSheet();

                $formattedDate = Carbon::createFromDate($this->year, $this->month)->isoFormat('MMMM') . ' - ' . $this->year;
                $worksheet->setCellValue('F5', ucfirst($formattedDate));

                $worksheet->setCellValue('C8', collect($this->vouchers[0]['invoices'])->sum('importe_total'));
                $worksheet->setCellValue('C9', collect($this->vouchers[2]['creditNotes'])->sum('valor_modificacion'));
                $worksheet->setCellValue('C10', collect($this->vouchers[3]['debitNotes'])->sum('valor_modificacion'));
                $worksheet->setCellValue('C11', collect($this->vouchers[1]['retentions'])->sum('total_retenido'));

                $worksheet->setCellValue('C13', $this->downloadedVouchers);
                $worksheet->setCellValue('C14', $this->sriVouchers);
                $worksheet->setCellValue('C15', $this->providers);
                $worksheet->setCellValue('E8', count($this->vouchers[0]['invoices']));
                $worksheet->setCellValue('E9', count($this->vouchers[2]['creditNotes']));
                $worksheet->setCellValue('E10', count($this->vouchers[3]['debitNotes']));
                $worksheet->setCellValue('E11', count($this->vouchers[1]['retentions']));

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(1);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
            },
        ];
    }
    public function title(): string
    {
        return 'Summary';
    }
}
