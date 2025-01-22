<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebitNoteRequest;
use App\Http\Requests\DownloadInvoiceRequest;
use App\Interfaces\debitNote\DebitNoteRepository;
use App\Models\DebitNote;
use App\Models\Transaction;
use App\Services\VoucherDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DebitNoteController extends Controller
{
    private DebitNoteRepository $debitNoteRepository;
    private VoucherDownload $voucherDownload;

    public function __construct(DebitNoteRepository $debitNoteRepository, VoucherDownload $voucherDownload){
        $this->debitNoteRepository = $debitNoteRepository;
        $this->voucherDownload = $voucherDownload;
    }

    public function index(Request $request, string $ruc)
    {
        return response()->json($this->debitNoteRepository->getByRuc($ruc, $request->header('X-Tenant')), 200);
    }

    public function store(DebitNoteRequest $request){
        return response()->json($this->debitNoteRepository->create($request->getDebitNoteFromRequest()));
    }

    public function downloadSingle(Request $request, string $id){
        $invoice = Http::get(config('services.tygor_microservice_url.url') . '/debit-note/show/' . $id)->json();
        $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
        return $this->voucherDownload->downloadSingle($invoice, $transaction, $request->type, "ND");
    }

    public function downloadRange(Request $request, string $ruc){
        $data[] = [
            'ruc' => $ruc,
            'month' => $request->month,
            'year' => $request->year,
            'tenant_id' => $request->header('X-Tenant'),
        ];

        $debitNotes = Http::post(config('services.tygor_microservice_url.url') . '/debit-note/range', $data);
        if ($debitNotes->status() > 299 && $debitNotes->status() < 200) {
            return response()->json(['message' => 'Error al descargar los documentos'], $debitNotes->status());
        } else {
            $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
            return $this->voucherDownload->downloadRange(collect(json_decode($debitNotes)), $transaction, $request->type, "ND");
        }
    }
}
