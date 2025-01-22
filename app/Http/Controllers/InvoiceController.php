<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Interfaces\invoice\InvoiceRepository;
use App\Models\Invoice;
use App\Services\VoucherDownload;
use Illuminate\Http\Request;
use App\Exports\DocumentsExport;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    private InvoiceRepository $invoiceRepository;
    private VoucherDownload $voucherDownload;

    public function __construct(InvoiceRepository $invoiceRepository, VoucherDownload $voucherDownload)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->voucherDownload = $voucherDownload;
    }

    public function index(Request $request, string $ruc)
    {
        return response()->json($this->invoiceRepository->getByRuc($ruc, $request->header('X-Tenant')), 200);
    }

    public function store(InvoiceRequest $request)
    {
        return response()->json($this->invoiceRepository->create($request->getInvoiceFromRequest()), 201);
    }

    public function downloadSingle(Request $request, string $id)
    {
        $invoice = Http::get(config('services.tygor_microservice_url.url') . '/invoices/show/' . $id)->json();
        $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
        return $this->voucherDownload->downloadSingle($invoice, $transaction, $request->type, "FAC");
    }

    public function downloadRange(Request $request, string $ruc)
    {
        $data = [
            'ruc' => $ruc,
            'month' => $request->month,
            'year' => $request->year,
            'tenant_id' => $request->header('X-Tenant'),
        ];
        $invoices = Http::post(config('services.tygor_microservice_url.url') . '/invoices/range', $data);
        if ($invoices->status() > 299 && $invoices->status() < 200) {
            return response()->json(['message' => 'Error al descargar los documentos'], $invoices->status());
        }

        $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
        return $this->voucherDownload->downloadRange(collect(json_decode($invoices)), $transaction, $request->type, "FAC");
    }

    public function export(Request $request)
    {
        // $request->validate([
        //     'year' => 'required',
        //     'month' => 'required',
        //     'ruc' => 'required'
        // ]);

        return Excel::download(new DocumentsExport($request->header("X-Tenant"), $request->year, $request->month, $request->ruc), 'vouchers.xlsx');
    }
}
