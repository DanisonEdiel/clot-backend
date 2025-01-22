<?php

namespace App\Services;

use App\Exceptions\MessageExceptions;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;


class VoucherDownload
{

    public function downloadSingle($voucher, object $transaction, string $type, $invoiceType)
    {
        Log::info($voucher);
        if (!$voucher) {
            throw new MessageExceptions([
                'Voucher not found!',
            ]);
        }

        try {
            DB::beginTransaction();

            if ($transaction->transactions <= 0) {
                throw new MessageExceptions([
                    'Limite de transacciones alcanzado!',
                ]);
            }

            if ($type === 'xml') {
                $path = str_replace('https://tygor-bots.nyc3.digitaloceanspaces.com', '', $voucher['xmlUrl']);
                $mimeType = Storage::mimeType($path);
                $fileName = basename($path);
            } else {
                $path = str_replace('https://tygor-bots.nyc3.digitaloceanspaces.com', '', $voucher['pdfUrl']);
                $mimeType = Storage::mimeType($path);
                $fileName = basename($path);
            }
            $voucherInTygor = Voucher::where('tenant_id', $transaction->tenant_id)->where('type', $type)->firstWhere('access_key', $voucher['clave_acceso']);

            if (!$voucherInTygor) {
                Voucher::create([
                   'access_key' => $voucher['clave_acceso'],
                   'type' => $invoiceType,
                   'tenant_id' => $transaction->tenant_id
                ]);
                $transaction->transactions = (int)$transaction->transactions - 1;
                $transaction->save();

            }

            $file = Storage::disk('spaces')->get($path);
            DB::commit();

            return response($file, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function downloadRange($vouchers, object $transaction, string $type, $invoiceType)
    {

        $documents = Voucher::where('tenant_id', $transaction->tenant_id)->where('type', $type)->get();

        if ($vouchers->isEmpty()) {
            throw new MessageExceptions([
                'No se encontraron comprobantes en el rango especificado',
            ]);
        }

        try {

            if ($transaction->transactions <= 0) {
                throw new MessageExceptions([
                    'Limite de transacciones alcanzado!',
                ]);
            }

            $files = [];
            foreach ($vouchers as $voucher) {
                if ($transaction->transactions <= 0) {
                    break;
                }

                if ($type === 'xml') {
                    $path = str_replace('https://tygor-bots.nyc3.digitaloceanspaces.com', '', $voucher->xmlUrl);
                    $files[] = $this->getFile($path);
                } elseif ($type === 'pdf') {
                    $path = str_replace('https://tygor-bots.nyc3.digitaloceanspaces.com', '', $voucher->pdfUrl);
                    $files[] = $this->getFile($path);
                } else {
                    $pathXml = str_replace('https://tygor-bots.nyc3.digitaloceanspaces.com', '', $voucher->xmlUrl);
                    $pathPdf = str_replace('https://tygor-bots.nyc3.digitaloceanspaces.com', '', $voucher->pdfUrl);
                    $files[] = $this->getFile($pathXml);
                    $files[] = $this->getFile($pathPdf);
                }
                $voucherInTygor = $documents->firstWhere('access_key', $voucher->clave_acceso);

                if (!$voucherInTygor) {
                    Voucher::create([
                        'access_key' => $voucher->clave_acceso,
                        'type' => $invoiceType,
                        'tenant_id' => $transaction->tenant_id
                    ]);
                    $transaction->transactions = (int)$transaction->transactions - 1;
                    $transaction->save();

                }
            }

            $zip = new ZipArchive();
            $zipFileName = 'comprobantes.zip';
            $zipFilePath = storage_path($zipFileName);

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($files as $file) {
                    $zip->addFromString($file['fileName'], $file['file']);
                }
                $zip->close();
            } else {
                DB::rollBack();
                throw new MessageExceptions([
                    'No se pudo obtener los comprobantes!',
                ]);
            }

            DB::commit();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
                'error' => $th->getMessage()
            ], 500);
        }
    }


    private function getFile($path)
    {
        $mimeType = Storage::mimeType($path);
        $fileName = basename($path);
        $file = Storage::disk('spaces')->get($path);
        return [
            'file' => $file,
            'mimeType' => $mimeType,
            'fileName' => $fileName,
        ];
    }
}
