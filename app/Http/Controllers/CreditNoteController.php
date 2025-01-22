<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditNoteRequest;
use App\Http\Requests\DownloadInvoiceRequest;
use App\Interfaces\creditNote\CreditNoteRepository;
use App\Models\CreditNote;
use App\Models\Transaction;
use App\Services\VoucherDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreditNoteController extends Controller
{
    private CreditNoteRepository $creditNoteRepository;
    private VoucherDownload $voucherDownload;

    public function __construct(CreditNoteRepository $creditNoteRepository, VoucherDownload $voucherDownload)
    {
        $this->creditNoteRepository = $creditNoteRepository;
        $this->voucherDownload = $voucherDownload;
    }

    public function index(Request $request, string $ruc)
    {
        return response()->json($this->creditNoteRepository->getByRuc($ruc, $request->header('X-Tenant')), 200);
    }

    public function store(CreditNoteRequest $request)
    {
        $creditNote = $this->creditNoteRepository->create($request->getCreditNoteFromRequest());

        return response()->json($creditNote, 201);
    }

    public function downloadSingle(Request $request, string $id)
    {
        $creditNote = Http::get(config('services.tygor_microservice_url.url') . '/credit-note/' . $id)->json();
        $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
        return $this->voucherDownload->downloadSingle($creditNote, $transaction, $request->type, "NC");
    }

    public function downloadRange(Request $request, string $ruc)
    {
        $data = [
            'ruc' => $ruc,
            'month' => $request->month,
            'year' => $request->year,
            'tenant_id' => $request->header('X-Tenant'),
        ];

        $creditNotes = Http::post(config('services.tygor_microservice_url.url') . '/credit-note/range', $data);
        if ($creditNotes->status() > 299 && $creditNotes->status() < 200) {
            return response()->json(['message' => 'Error al descargar los documentos'], $creditNotes->status());
        } else {
            $transaction = Transaction::firstWhere('tenant_id', $request->header('X-Tenant'));
            return $this->voucherDownload->downloadRange(collect(json_decode($creditNotes)), $transaction, $request->type, "NC");
        }
    }
}
