<?php

namespace App\Exports;

use App\Models\Ruc;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;

class DebitNoteExport implements WithEvents, WithTitle

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
        $this->templatePath = storage_path('templates/ND.xlsx');
        $this->loadData();

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
                foreach ($this->vouchers as $debitNote) {
                    $dateEmisionPart = explode('-', $debitNote->fecha_emision);
                    if (count($dateEmisionPart) === 3){
                        $worksheet->setCellValue("B{$row}", $dateEmisionPart[0]);
                        $worksheet->setCellValue("C{$row}", $dateEmisionPart[1]);
                    }
                    $worksheet->setCellValue("D{$row}", $debitNote->razon_social_emisor);
                    $worksheet->setCellValue("E{$row}", $debitNote->nombre_comercial_emisor);
                    $worksheet->setCellValue("F{$row}", $debitNote->ruc_emisor);
                    $worksheet->setCellValue("G{$row}", $debitNote->clave_acceso);
                    $worksheet->setCellValue("H{$row}", $debitNote->clave_acceso);
                    $worksheet->setCellValue("I{$row}", $debitNote->fecha_autorizacion);
                    $worksheet->setCellValue("J{$row}", '05');
                    $comprobanteParts = explode('-', $debitNote->serie_comprobante);

                    if (count($comprobanteParts) === 3){
                        $worksheet->setCellValue("K{$row}", $comprobanteParts[0]);
                        $worksheet->setCellValue("L{$row}", $comprobanteParts[1]);
                        $worksheet->setCellValue("M{$row}", $comprobanteParts[2]);
                    }
                    $worksheet->setCellValue("N{$row}", $debitNote->direccion_matriz);
                    $worksheet->setCellValue("O{$row}", $debitNote->fecha_emision);
                    $worksheet->setCellValue("P{$row}", $debitNote->direccion_establecimiento);
                    $worksheet->setCellValue("Q{$row}", $debitNote->tipo_identificacion_comprador);
                    $worksheet->setCellValue("R{$row}", $debitNote->razon_social_comprador);
                    $worksheet->setCellValue("S{$row}", $debitNote->identificacion_comprador);
                    $worksheet->setCellValue("T{$row}", $debitNote->contribuyente_especial);
                    $worksheet->setCellValue("U{$row}", $debitNote->obligado_llevar_contabilidad);
                    $worksheet->setCellValue("V{$row}", $debitNote->cod_doc_modificado);
                    $worksheet->setCellValue("W{$row}", $debitNote->num_doc_modificado);
                    $worksheet->setCellValue("X{$row}", $debitNote->fecha_emision_doc_modificado);
                    $worksheet->setCellValue("Y{$row}", $debitNote->total_sin_impuestos);
                    $worksheet->setCellValue("Z{$row}", $debitNote->valor_modificacion);
                    $worksheet->setCellValue("AA{$row}", $this->getTax($debitNote,'0', false));
                    $worksheet->setCellValue("AB{$row}", $this->getTax($debitNote,'0', true));
                    $worksheet->setCellValue("AC{$row}", $this->getTax($debitNote,'8', type: false));
                    $worksheet->setCellValue("AD{$row}", $this->getTax($debitNote,'8', true));
                    $worksheet->setCellValue("AE{$row}", $this->getTax($debitNote,'5', false));
                    $worksheet->setCellValue("AF{$row}", $this->getTax($debitNote,'5', true));
                    $worksheet->setCellValue("AG{$row}", $this->getTax($debitNote,'2', false));
                    $worksheet->setCellValue("AH{$row}", $this->getTax($debitNote,'2', true));
                    $worksheet->setCellValue("AI{$row}", $this->getTax($debitNote,'10', false));
                    $worksheet->setCellValue("AJ{$row}", $this->getTax($debitNote,'10', true));
                    $worksheet->setCellValue("AK{$row}", $this->getTax($debitNote,'3', false));
                    $worksheet->setCellValue("AL{$row}", $this->getTax($debitNote,'3', true));
                    $worksheet->setCellValue("AM{$row}", $this->getTax($debitNote,'4', false));
                    $worksheet->setCellValue("AN{$row}", $this->getTax($debitNote,'4', true));
                    $worksheet->setCellValue("AO{$row}", $this->getTax($debitNote,'6', false));
                    $worksheet->setCellValue("AP{$row}", $this->getTax($debitNote,'6', true));
                    $worksheet->setCellValue("AQ{$row}", $this->getTax($debitNote,'7', false));
                    $worksheet->setCellValue("AR{$row}", $this->getTax($debitNote,'7', true));
                    $worksheet->setCellValue("AS{$row}", $debitNote->valor_ice);
                    $worksheet->setCellValue("AT{$row}", $debitNote->valor_irbpnr);
                    $worksheet->setCellValue("AU{$row}", $debitNote->razones);
                    $worksheet->setCellValue("AV{$row}", $debitNote->valores);
                    $worksheet->setCellValue("AW{$row}", $debitNote->razones);
                    $worksheet->setCellValue("AX{$row}", $debitNote->valores);
                    $worksheet->setCellValue("AY{$row}", $debitNote->motivos_adicionales);
                    $row++;
                }

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(4);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
            }
        ];
    }
    public function title(): string
    {
        return 'ND';
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
