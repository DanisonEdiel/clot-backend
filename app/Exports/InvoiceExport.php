<?php

namespace App\Exports;

use App\Models\Ruc;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvoiceExport implements WithEvents, WithTitle
{
    private $vouchers;
    private $templatePath;
    private $invoiceTaxesSummary;
    private $downloadedVouchers;
    private $ruc;
    private $tenantId;

    public function __construct(array $vouchers, $downloadedVouchers, $ruc, $tenantId)
    {
        $this->vouchers = $vouchers;
        $this->downloadedVouchers = $downloadedVouchers;
        $this->ruc = Ruc::where('tenant_id', $tenantId)->where('ruc', $ruc)->first();
        $this->templatePath = storage_path('templates/Factura.xlsx');
        $this->loadData();
        // dd($this->ruc);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = IOFactory::load($this->templatePath);
                $worksheet = $spreadsheet->getActiveSheet();

                $worksheet->setCellValue('D6', $this->ruc->ruc);
                $worksheet->setCellValue('D7', $this->ruc->name);
                $worksheet->setCellValue('D8', now());
                $worksheet->setCellValue('D9', $this->downloadedVouchers);

                $row = 19;
                foreach ($this->vouchers as $factura) {
                    $dateEmisionPart = explode('-', $factura->fecha_emision);
                    if (count($dateEmisionPart) === 3) {
                        $worksheet->setCellValue("B{$row}", $dateEmisionPart[0]);
                        $worksheet->setCellValue("C{$row}", $dateEmisionPart[1]);
                    }
                    $worksheet->setCellValue("D{$row}", $factura->razon_social_emisor);
                    $worksheet->setCellValue("E{$row}", $factura->nombre_comercial_emisor);
                    $worksheet->setCellValue("F{$row}", $factura->ruc_emisor);
                    $worksheet->setCellValue("G{$row}", '01');
                    $comprobanteParts = explode('-', $factura->serie_comprobante);

                    if (count($comprobanteParts) === 3) {
                        $worksheet->setCellValue("H{$row}", $comprobanteParts[0]);
                        $worksheet->setCellValue("I{$row}", $comprobanteParts[1]);
                        $worksheet->setCellValue("J{$row}", $comprobanteParts[2]);
                    }
                    $worksheet->setCellValue("K{$row}", $factura->direccion_matriz);
                    $worksheet->setCellValue("L{$row}", $factura->fecha_emision);
                    $worksheet->setCellValue("M{$row}", $factura->direccion_establecimiento);
                    $worksheet->setCellValue("N{$row}", $factura->contribuyente_especial);
                    $worksheet->setCellValue("O{$row}", $factura->obligado_llevar_contabilidad);
                    $worksheet->setCellValue("P{$row}", $factura->razon_social_comprador);
                    $worksheet->setCellValue("Q{$row}", $factura->identificacion_comprador);
                    $worksheet->setCellValue("R{$row}", $factura->total_sin_impuestos);
                    $worksheet->setCellValue("S{$row}", $factura->total_descuento);
                    $worksheet->setCellValue("T{$row}", $factura->propina);
                    $worksheet->setCellValue("U{$row}", $factura->importe_total);
                    $worksheet->setCellValue("V{$row}", $this->getTax($factura,'0', false));
                    $worksheet->setCellValue("W{$row}", $this->getTax($factura,'0', true));
                    $worksheet->setCellValue("X{$row}", $this->getTax($factura,'8', false));
                    $worksheet->setCellValue("Y{$row}", $this->getTax($factura,'8', true));
                    $worksheet->setCellValue("Z{$row}", $this->getTax($factura,'5', false));
                    $worksheet->setCellValue("AA{$row}", $this->getTax($factura,'5', true));
                    $worksheet->setCellValue("AB{$row}", $this->getTax($factura,'2', false));
                    $worksheet->setCellValue("AC{$row}", $this->getTax($factura,'2', true));
                    $worksheet->setCellValue("AD{$row}", $this->getTax($factura,'10', false));
                    $worksheet->setCellValue("AE{$row}", $this->getTax($factura,'10', true));
                    $worksheet->setCellValue("AF{$row}", $this->getTax($factura,'3', false));
                    $worksheet->setCellValue("AG{$row}", $this->getTax($factura,'3', true));
                    $worksheet->setCellValue("AH{$row}", $this->getTax($factura,'4', false));
                    $worksheet->setCellValue("AI{$row}", $this->getTax($factura,'4', true));
                    $worksheet->setCellValue("AJ{$row}", $this->getTax($factura,'6', false));
                    $worksheet->setCellValue("AK{$row}", $this->getTax($factura,'6', true));
                    $worksheet->setCellValue("AL{$row}", $this->getTax($factura,'7', false));
                    $worksheet->setCellValue("AM{$row}", $this->getTax($factura,'7', true));
                    $worksheet->setCellValue("AN{$row}", $factura->valor_ice);
                    $worksheet->setCellValue("AO{$row}", $factura->valor_irbpnr);
                    $worksheet->setCellValue("AP{$row}", $factura->clave_acceso);
                    $worksheet->setCellValue("AQ{$row}", $factura->clave_acceso);
                    $worksheet->setCellValue("AR{$row}", $factura->fecha_autorizacion);
                    $row++;
                }

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(2);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
            },
        ];
    }

    public function title(): string
    {
        return 'Factura';
    }

    public function loadData()
    {
        if ($this->vouchers) {
            foreach ($this->vouchers as $voucher) {
                foreach ($voucher->detalles as $detail) {
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
        } else {
            $this->invoiceTaxesSummary['1000000000000000'] = [
                'total_impuesto' => 0,
                'subtotal' => 0,
            ];
        }
    }

    public function getTax($voucher,$taxCode, $type){
        foreach ($voucher->detalles as $detail) {
            if (!isset($invoiceTaxesSummary[(int)$detail->codigo_porcentaje])) {
                $invoiceTaxesSummary[(int)$detail->codigo_porcentaje] = [
                    'total_impuesto' => 0,
                    'subtotal' => 0,
                ];
            }
            $invoiceTaxesSummary[(int)$detail->codigo_porcentaje]['total_impuesto'] += $detail->total_impuesto;
            $invoiceTaxesSummary[(int)$detail->codigo_porcentaje]['subtotal'] += $detail->subtotal;
        } 
        $subtotal = 0;
        if (isset($invoiceTaxesSummary[(int)$taxCode])) {
            if ($type == true) {
                return $invoiceTaxesSummary[$taxCode]['total_impuesto'];
            } else {
                return $invoiceTaxesSummary[$taxCode]['subtotal'];
            }
        }
        return (float)number_format($subtotal, '2');
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
