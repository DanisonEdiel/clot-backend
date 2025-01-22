<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Ruc;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;

class RetentionExport implements WithEvents, WithTitle

{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $vouchers;

    private $templatePath;
    private $invoiceTaxesSummary;
    private $downloadedVouchers;
    private $ruc;


    public function __construct(array $vouchers, $downloadedVouchers, $ruc, $tenantId)
    {
        $this->vouchers = $vouchers;
        $this->downloadedVouchers = $downloadedVouchers;
        $this->ruc = Ruc::where('tenant_id', $tenantId)->where('ruc', $ruc)->first();
        $this->templatePath = storage_path('templates/R.xlsx');

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
                foreach ($this->vouchers as $retention) {
                    foreach ($retention->detalles as $detalle) {
                        $dateEmisionPart = explode('-', $retention->fecha_emision);
                        if (count($dateEmisionPart) === 3){
                            $worksheet->setCellValue("B{$row}", $dateEmisionPart[0]);
                            $worksheet->setCellValue("C{$row}", $dateEmisionPart[1]);
                        }
                        $worksheet->setCellValue("D{$row}", $retention->razon_social_emisor);
                        $worksheet->setCellValue("E{$row}", $retention->nombre_comercial_emisor);
                        $worksheet->setCellValue("F{$row}", $retention->ruc_emisor);
                        $worksheet->setCellValue("G{$row}", $retention->clave_acceso);
                        $worksheet->setCellValue("H{$row}", $retention->clave_acceso);
                        $worksheet->setCellValue("I{$row}", $retention->fecha_autorizacion);
                        $worksheet->setCellValue("J{$row}", '07');
                        $comprobanteParts = explode('-', $retention->serie_comprobante);

                        if (count($comprobanteParts) === 3){
                            $worksheet->setCellValue("K{$row}", $comprobanteParts['0']);
                            $worksheet->setCellValue("L{$row}", $comprobanteParts['1']);
                            $worksheet->setCellValue("M{$row}", $comprobanteParts['2']);
                        }
                        $worksheet->setCellValue("N{$row}", $retention->direccion_matriz);
                        $worksheet->setCellValue("O{$row}", $retention->fecha_emision);
                        $worksheet->setCellValue("P{$row}", $retention->direccion_establecimiento);
                        $worksheet->setCellValue("Q{$row}", $retention->contribuyente_especial);
                        $worksheet->setCellValue("R{$row}", $retention->obligado_llevar_contabilidad);
                        $worksheet->setCellValue("S{$row}", $retention->tipo_identificacion_sujeto_retention);
                        $worksheet->setCellValue("T{$row}", $retention->razon_social_sujeto_retenido);
                        $worksheet->setCellValue("U{$row}", $retention->identificacion_sujeto_retenido);
                        $worksheet->setCellValue("V{$row}", $retention->periodo_fiscal);
                        $worksheet->setCellValue("W{$row}", $retention->total_retenido);
                        $worksheet->setCellValue("X{$row}", $detalle->codigo_retencion);
                        $worksheet->setCellValue("Y{$row}", '0');
                        $worksheet->setCellValue("Z{$row}", $detalle->codigo_retencion);
                        $worksheet->setCellValue("AA{$row}", $detalle->base_imponible);
                        $worksheet->setCellValue("AB{$row}", $detalle->porcentaje_retencion);
                        $worksheet->setCellValue("AC{$row}", $detalle->valor_retenido);
                        $worksheet->setCellValue("AD{$row}", $detalle->cod_documento_sustento);
                        $worksheet->setCellValue("AE{$row}", $detalle->num_documento_sustento);
                        $worksheet->setCellValue("AF{$row}", $detalle->fecha_emision_sustento);
                        $worksheet->setCellValue("AG{$row}", isset($detalle->fecha_registro_contable)  ? $detalle->fecha_registro_contable : 'N/A');
                        $worksheet->setCellValue("AH{$row}", isset($detalle->num_aut_doc_sustento) ? $detalle->num_aut_doc_sustento : 'N/A');
                        $row++;
                    }
                }

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(5);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
            }
        ];
    }

    public function title(): string
    {
        return 'R';
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

    private function getInvoiceTaxeSummary($taxCode, $type)
    {
        $subtotal = 0;
        if (isset($this->invoiceTaxesSummary[(int)$taxCode])) {
            if ($type == true) {
                return $this->invoiceTaxesSummary[$taxCode]['total_impuesto'];
            } else {
                return $this->invoiceTaxesSummary[$taxCode]['subtotal'];
            }
        }
        return (float)number_format($subtotal, '2');
    }
}
