<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Invoice;
use App\Models\Retention;
use App\Models\Ruc;
use App\Models\Tenant;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getTenantInfo(Request $request)
    {
        try {
            $tenant = Tenant::find($request->header('X-Tenant'));
            $tenant->transactions;
            $tenant->subscription;

            $rucs = Ruc::where('tenant_id', $tenant->id)->count();

            $invoices = Invoice::where('tenant_id', $tenant->id)->orderBy('created_at', 'desc')->take(5)->get();
            $creditNotes = CreditNote::where('tenant_id', $tenant->id)->orderBy('created_at', 'desc')->take(5)->get();
            $retentions = Retention::where('tenant_id', $tenant->id)->orderBy('created_at', 'desc')->take(5)->get();
            $debitNotes = DebitNote::where('tenant_id', $tenant->id)->orderBy('created_at', 'desc')->take(5)->get();

            return response()->json([
                'tenant' => $tenant,
                'rucs' => $rucs,
                'invoices' => $invoices,
                'creditNotes' => $creditNotes,
                'retentions' => $retentions,
                'debitNotes' => $debitNotes,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
