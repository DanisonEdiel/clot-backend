<?php

namespace App\Exports;

use App\Models\Ruc;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CreditNoteExport implements WithEvents, WithTitle
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
        $this->templatePath = storage_path('templates/Nota-de-Credito.xlsx');
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
                foreach ($this->vouchers as $creditNote) {
                    $dateEmisionPart = explode('-', $creditNote->fecha_emision);
                    if (count($dateEmisionPart) === 3){
                        $worksheet->setCellValue("B{$row}", $dateEmisionPart[0]);
                        $worksheet->setCellValue("C{$row}", $dateEmisionPart[1]);
                    }
                    $worksheet->setCellValue("D{$row}", $creditNote->razon_social_emisor);
                    $worksheet->setCellValue("E{$row}", $creditNote->nombre_comercial_emisor);
                    $worksheet->setCellValue("F{$row}", $creditNote->ruc_emisor);
                    $worksheet->setCellValue("G{$row}", $creditNote->clave_acceso);
                    $worksheet->setCellValue("H{$row}", $creditNote->clave_acceso);
                    $worksheet->setCellValue("I{$row}", $creditNote->fecha_autorizacion);
                    $worksheet->setCellValue("J{$row}", '04');
                    $comprobanteParts = explode('-', $creditNote->serie_comprobante);

                    if (count($comprobanteParts) === 3){
                        $worksheet->setCellValue("K{$row}", $comprobanteParts[0]);
                        $worksheet->setCellValue("L{$row}", $comprobanteParts[1]);
                        $worksheet->setCellValue("M{$row}", $comprobanteParts[2]);
                    }
                    $worksheet->setCellValue("N{$row}", $creditNote->direccion_matriz);
                    $worksheet->setCellValue("O{$row}", $creditNote->fecha_emision);
                    $worksheet->setCellValue("P{$row}", $creditNote->direccion_establecimiento);
                    $worksheet->setCellValue("Q{$row}", $creditNote->tipo_identificacion_comprador);
                    $worksheet->setCellValue("R{$row}", $creditNote->razon_social_comprador);
                    $worksheet->setCellValue("S{$row}", $creditNote->identificacion_comprador);
                    $worksheet->setCellValue("T{$row}", $creditNote->contribuyente_especial);
                    $worksheet->setCellValue("U{$row}", $creditNote->obligado_llevar_contabilidad);
                    $worksheet->setCellValue("V{$row}", $creditNote->cod_doc_modificado);
                    $worksheet->setCellValue("W{$row}", $creditNote->num_doc_modificado);
                    $worksheet->setCellValue("X{$row}", $creditNote->fecha_emision_doc_modificado);
                    $worksheet->setCellValue("Y{$row}", $creditNote->total_sin_impuestos);
                    $worksheet->setCellValue("Z{$row}", $creditNote->valor_modificacion);
                    $worksheet->setCellValue("AA{$row}", $creditNote->motivo);
                    $worksheet->setCellValue("AB{$row}", $this->getTax($creditNote,'0', false));
                    $worksheet->setCellValue("AC{$row}", $this->getTax($creditNote,'0', true));
                    $worksheet->setCellValue("AD{$row}", $this->getTax($creditNote,'8', type: false));
                    $worksheet->setCellValue("AE{$row}", $this->getTax($creditNote,'8', true));
                    $worksheet->setCellValue("AF{$row}", $this->getTax($creditNote,'5', false));
                    $worksheet->setCellValue("AG{$row}", $this->getTax($creditNote,'5', true));
                    $worksheet->setCellValue("AH{$row}", $this->getTax($creditNote,'2', false));
                    $worksheet->setCellValue("AI{$row}", $this->getTax($creditNote,'2', true));
                    $worksheet->setCellValue("AJ{$row}", $this->getTax($creditNote,'10', false));
                    $worksheet->setCellValue("AK{$row}", $this->getTax($creditNote,'10', true));
                    $worksheet->setCellValue("AL{$row}", $this->getTax($creditNote,'3', false));
                    $worksheet->setCellValue("AM{$row}", $this->getTax($creditNote,'3', true));
                    $worksheet->setCellValue("AN{$row}", $this->getTax($creditNote,'4', false));
                    $worksheet->setCellValue("AO{$row}", $this->getTax($creditNote,'4', true));
                    $worksheet->setCellValue("AP{$row}", $this->getTax($creditNote,'6', false));
                    $worksheet->setCellValue("AQ{$row}", $this->getTax($creditNote,'6', true));
                    $worksheet->setCellValue("AR{$row}", $this->getTax($creditNote,'7', false));
                    $worksheet->setCellValue("AS{$row}", $this->getTax($creditNote,'7', true));
                    $worksheet->setCellValue("AT{$row}", $creditNote->valor_ice);
                    $worksheet->setCellValue("AU{$row}", $creditNote->valor_irbpnr);
                    $row++;
                }

                $event->sheet->getDelegate()->getParent()->removeSheetByIndex(3);
                $event->sheet->getDelegate()->getParent()->addExternalSheet($worksheet);
            }
        ];
    }

    public function title(): string
    {
        return 'NC';
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
