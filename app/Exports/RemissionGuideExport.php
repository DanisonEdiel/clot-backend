<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Events\AfterSheet;

class RemissionGuideExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithTitle

{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $tenantId;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function collection()
    {
        return Invoice::select(
            'ruc_emisor',
            'razon_social_emisor',
            'tipo_comprobante',
            'serie_comprobante',
            'clave_acceso',
            'fecha_autorizacion',
            'fecha_emision',
            'identificacion_receptor',
            'numero_documento_modificado',
            'xmlUrl',
            'pdfUrl',
            'valor_sin_impuestos',
            'iva',
            'importe_total'
        )->whereTenantId($this->tenantId)->whereTipoComprobante('GR')->get();
    }
    public function headings(): array
    {
        return [
            'Ruc_emisor',
            'Razon_social_emisor',
            'Tipo_comprobante',
            'Serie_comprobante',
            'Clave_acceso',
            'Fecha_autorizacion',
            'Fecha_emision',
            'identificacion_receptor',
            'Numero_documento_modificado',
            'XmlUrl',
            'PdfUrl',
            'Valor_sin_impuestos',
            'Iva',
            'Importe_total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Encabezados en negrita
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_GRADIENT_LINEAR,
                        'color' => ['argb' => Color::COLOR_YELLOW],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->setAutoFilter('A1:N1');
                $sheet->getStyle('A1:N' . $sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                foreach (range('A', $sheet->getHighestColumn()) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
    public function title(): string
    {
        return 'Guía de Remisión';
    }
}
