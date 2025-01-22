<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloadInvoiceRequest;
use App\Http\Requests\RetentionRequest;
use App\Interfaces\retention\RetentionRepository;
use App\Models\Retention;
use App\Models\Transaction;
use App\Services\VoucherDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RetentionController extends Controller
{
    private RetentionRepository $repository;
    private VoucherDownload $voucherDownload;

    public function __construct(RetentionRepository $repository, VoucherDownload $voucherDownload)
    {
        $this->repository = $repository;
        $this->voucherDownload = $voucherDownload;
    }

    public function index(Request $request, string $ruc)
    {
        return response()->json($this->repository->getByRuc($ruc, $request->header('X-Tenant')), 200);
    }

    public function store(RetentionRequest $request)
    {
        return response()->json($this->repository->create($request->getRetentionFromRequest()), 201);
    }

    public function downloadSingle(Request $request, string $id)
    {
        $invoice = Http::get(config('services.tygor_microservice_url.url') . '/retentions/show/' . $id)->json();
        $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
        return $this->voucherDownload->downloadSingle($invoice, $transaction, $request->type, "RT");
    }

    public function downloadRange(Request $request, string $ruc)
    {
        $data = [
            'ruc' => $ruc,
            'month' => $request->month,
            'year' => $request->year,
            'tenant_id' => $request->header('X-Tenant'),
        ];

        $retentions = Http::post(config('services.tygor_microservice_url.url') . '/retentions/range', $data);
        if ($retentions->status() > 299 && $retentions->status() < 200) {
            return response()->json(['message' => 'Error al descargar los documentos'], $retentions->status());
        }

        $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
        return $this->voucherDownload->downloadRange(collect(json_decode($retentions)), $transaction, $request->type, "RT");
    }
}
