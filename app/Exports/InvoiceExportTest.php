<?php

namespace App\Exports;

use App\Services\GetVouchersService;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InvoiceExportTest implements WithEvents
{
    private $vouchers;
    private $templatePath;
    private $invoiceTaxesSummary;
    private $tenantId;
    private $year;
    private $month;
    private $ruc;
    public function __construct($tenantId, $year, $month, $ruc)
    {
        $this->templatePath = storage_path('templates/Reporte-Tygor.xlsx');
        $this->tenantId = $tenantId;
        $this->year = $year;
        $this->month = $month;
        $this->ruc = $ruc;
        $this->loadData();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = IOFactory::load($this->templatePath);
                $worksheet = $spreadsheet->getSheetByName('Factura');
                $worksheetCredit = $spreadsheet->getSheetByName('Notas_de_Crédito');

                $worksheet->setCellValue('B6', 'name');
                $worksheet->setCellValue('B7', 'Usuario: NombreUsuario');
                $worksheet->setCellValue('B8', 'Fecha de Descarga: ' . now());

                $worksheetCredit->setCellValue('B6', 'name');
                $worksheetCredit->setCellValue('B7', 'Usuario: NombreUsuario');
                $worksheetCredit->setCellValue('B8', 'Fecha de Descarga: ' . now());

                $row = 19;
                foreach ($this->vouchers as $factura) {
                    $worksheet->setCellValue("B{$row}", $factura['fecha_autorizacion']);
                    $worksheet->setCellValue("C{$row}", $factura['fecha_autorizacion']);
                    $worksheet->setCellValue("D{$row}", $factura['razon_social_emisor']);
                    $worksheet->setCellValue("E{$row}", $factura['nombre_comercial_emisor']);
                    $worksheet->setCellValue("F{$row}", $factura['ruc_emisor']);
                    $worksheet->setCellValue("G{$row}", $factura['serie_comprobante']);
                    $worksheet->setCellValue("H{$row}", $factura['']);
                    $worksheet->setCellValue("I{$row}", $factura['']);
                    $worksheet->setCellValue("J{$row}", $factura['']);
                    $worksheet->setCellValue("K{$row}", $factura['direccion_matriz']);
                    $worksheet->setCellValue("L{$row}", $factura['fecha_emision']);
                    $worksheet->setCellValue("M{$row}", $factura['direccion_establecimiento']);
                    $worksheet->setCellValue("N{$row}", $factura['contribuyente_especial']);
                    $worksheet->setCellValue("O{$row}", $factura['obligado_llevar_contabilidad']);
                    $worksheet->setCellValue("P{$row}", $factura['razon_social_comprador']);
                    $worksheet->setCellValue("Q{$row}", $factura['identificacion_comprador']);
                    $worksheet->setCellValue("R{$row}", $factura['total_sin_impuestos']);
                    $worksheet->setCellValue("S{$row}", $factura['total_descuento']);
                    $worksheet->setCellValue("T{$row}", $factura['propina']);
                    $worksheet->setCellValue("U{$row}", $factura['importe_total']);
                    $worksheet->setCellValue("V{$row}", $this->getInvoiceTaxeSummary('0', false));
                    $worksheet->setCellValue("W{$row}", $this->getInvoiceTaxeSummary('0', true));
                    $worksheet->setCellValue("X{$row}", $this->getInvoiceTaxeSummary('8', false));
                    $worksheet->setCellValue("Y{$row}", $this->getInvoiceTaxeSummary('8', true));
                    $worksheet->setCellValue("Z{$row}", $this->getInvoiceTaxeSummary('5', false));
                    $worksheet->setCellValue("AA{$row}", $this->getInvoiceTaxeSummary('5', true));
                    $worksheet->setCellValue("AB{$row}", $this->getInvoiceTaxeSummary('2', false));
                    $worksheet->setCellValue("AC{$row}", $this->getInvoiceTaxeSummary('2', true));
                    $worksheet->setCellValue("AD{$row}", $this->getInvoiceTaxeSummary('10', false));
                    $worksheet->setCellValue("AE{$row}", $this->getInvoiceTaxeSummary('10', true));
                    $worksheet->setCellValue("AF{$row}", $this->getInvoiceTaxeSummary('3', false));
                    $worksheet->setCellValue("AG{$row}", $this->getInvoiceTaxeSummary('3', true));
                    $worksheet->setCellValue("AH{$row}", $this->getInvoiceTaxeSummary('4', false));
                    $worksheet->setCellValue("AI{$row}", $this->getInvoiceTaxeSummary('4', true));
                    $worksheet->setCellValue("AJ{$row}", $this->getInvoiceTaxeSummary('6', false));
                    $worksheet->setCellValue("AK{$row}", $this->getInvoiceTaxeSummary('6', true));
                    $worksheet->setCellValue("AL{$row}", $this->getInvoiceTaxeSummary('7', false));
                    $worksheet->setCellValue("AM{$row}", $this->getInvoiceTaxeSummary('7', true));
                    $worksheet->setCellValue("AN{$row}", $factura['valor_ice']);
                    $worksheet->setCellValue("AO{$row}", $factura['valor_irbpnr']);
                    $worksheet->setCellValue("AP{$row}", $factura['valor_irbpnr']);
                    $row++;
                }


                $rowA = 19;
                foreach ($this->vouchers as $factura) {
                    $worksheetCredit->setCellValue("B{$rowA}", $factura['fecha_autorizacion']);
                    $worksheetCredit->setCellValue("C{$rowA}", $factura['fecha_autorizacion']);
                    $worksheetCredit->setCellValue("D{$rowA}", $factura['razon_social_emisor']);
                    $worksheetCredit->setCellValue("E{$rowA}", $factura['nombre_comercial_emisor']);
                    $worksheetCredit->setCellValue("F{$rowA}", $factura['ruc_emisor']);
                    $worksheetCredit->setCellValue("G{$rowA}", $factura['serie_comprobante']);
                    $worksheetCredit->setCellValue("H{$rowA}", $factura['']);
                    $worksheetCredit->setCellValue("I{$rowA}", $factura['']);
                    $worksheetCredit->setCellValue("J{$rowA}", $factura['']);
                    $worksheetCredit->setCellValue("K{$rowA}", $factura['direccion_matriz']);
                    $worksheetCredit->setCellValue("L{$rowA}", $factura['fecha_emision']);
                    $worksheetCredit->setCellValue("M{$rowA}", $factura['direccion_establecimiento']);
                    $worksheetCredit->setCellValue("N{$rowA}", $factura['contribuyente_especial']);
                    $worksheetCredit->setCellValue("O{$rowA}", $factura['obligado_llevar_contabilidad']);
                    $worksheetCredit->setCellValue("P{$rowA}", $factura['razon_social_comprador']);
                    $worksheetCredit->setCellValue("Q{$rowA}", $factura['identificacion_comprador']);
                    $worksheetCredit->setCellValue("R{$rowA}", $factura['total_sin_impuestos']);
                    $worksheetCredit->setCellValue("S{$rowA}", $factura['total_descuento']);
                    $worksheetCredit->setCellValue("T{$rowA}", $factura['propina']);
                    $worksheetCredit->setCellValue("U{$rowA}", $factura['importe_total']);
                    $worksheetCredit->setCellValue("V{$rowA}", $this->getInvoiceTaxeSummary('0', false));
                    $worksheetCredit->setCellValue("W{$rowA}", $this->getInvoiceTaxeSummary('0', true));
                    $worksheetCredit->setCellValue("X{$rowA}", $this->getInvoiceTaxeSummary('8', false));
                    $worksheetCredit->setCellValue("Y{$rowA}", $this->getInvoiceTaxeSummary('8', true));
                    $worksheetCredit->setCellValue("Z{$rowA}", $this->getInvoiceTaxeSummary('5', false));
                    $worksheetCredit->setCellValue("AA{$rowA}", $this->getInvoiceTaxeSummary('5', true));
                    $worksheetCredit->setCellValue("AB{$rowA}", $this->getInvoiceTaxeSummary('2', false));
                    $worksheetCredit->setCellValue("AC{$rowA}", $this->getInvoiceTaxeSummary('2', true));
                    $worksheetCredit->setCellValue("AD{$rowA}", $this->getInvoiceTaxeSummary('10', false));
                    $worksheetCredit->setCellValue("AE{$rowA}", $this->getInvoiceTaxeSummary('10', true));
                    $worksheetCredit->setCellValue("AF{$rowA}", $this->getInvoiceTaxeSummary('3', false));
                    $worksheetCredit->setCellValue("AG{$rowA}", $this->getInvoiceTaxeSummary('3', true));
                    $worksheetCredit->setCellValue("AH{$rowA}", $this->getInvoiceTaxeSummary('4', false));
                    $worksheetCredit->setCellValue("AI{$rowA}", $this->getInvoiceTaxeSummary('4', true));
                    $worksheetCredit->setCellValue("AJ{$rowA}", $this->getInvoiceTaxeSummary('6', false));
                    $worksheetCredit->setCellValue("AK{$rowA}", $this->getInvoiceTaxeSummary('6', true));
                    $worksheetCredit->setCellValue("AL{$rowA}", $this->getInvoiceTaxeSummary('7', false));
                    $worksheetCredit->setCellValue("AM{$rowA}", $this->getInvoiceTaxeSummary('7', true));
                    $worksheetCredit->setCellValue("AN{$rowA}", $factura['valor_ice']);
                    $worksheetCredit->setCellValue("AO{$rowA}", $factura['valor_irbpnr']);
                    $worksheetCredit->setCellValue("AP{$rowA}", $factura['valor_irbpnr']);
                    $rowA++;
                }

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(0);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheetCredit);
            },
        ];
    }

    // public function title(): string
    // {
    //     return 'Factura';
    // }

    public function loadData()
    {
        $vouchersService = new GetVouchersService();
        $vouchers = $vouchersService->getVouchers($this->ruc, $this->month, $this->year, $this->tenantId);
        $this->vouchers = $vouchers[0]['invoices'];
        foreach (json_decode($this->vouchers[0]['detalles']) as $detail) {
            if (!isset($this->invoiceTaxesSummary[(int)$detail->codigo_porcentaje])) {
                $this->invoiceTaxesSummary[(int)$detail->codigo_porcentaje] = [
                    'total_impuesto' => 0,
                    'subtotal' => 0,
                ];
            }
            $this->invoiceTaxesSummary[(int)$detail->codigo_porcentaje]['total_impuesto'] += $detail->total_impuesto;
            $this->invoiceTaxesSummary[(int)$detail->codigo_porcentaje]['subtotal'] += $detail->subtotal;
        }
    }

    private function getInvoiceTaxeSummary($taxCode, $type)
    {
        $subtotal = 0;
        if (isset($this->invoiceTaxesSummary[(int)$taxCode])) {
            if ($type == false) {
                return $this->invoiceTaxesSummary[$taxCode]['total_impuesto'];
            } else {
                return $this->invoiceTaxesSummary[$taxCode]['subtotal'];
            }
        }
        return (float)number_format($subtotal, '2');
    }
}
