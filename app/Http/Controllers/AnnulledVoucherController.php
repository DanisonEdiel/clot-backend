<?php

namespace App\Http\Controllers;

use App\Services\anulated\AnnulledVouchersService;
use Illuminate\Http\Request;

class AnnulledVoucherController extends Controller
{
    private AnnulledVouchersService $service;

    public function __construct()
    {
        $this->service = new AnnulledVouchersService();
    }

    public function index(Request $request, string $ruc)
    {
        return response()->json($this->service->index($request->header('X-Tenant'), $ruc), 200);
    }

    public function show(Request $request)
    {
        return response()->json($this->service->changeStatus($request->access_key, $request->header('tenant_id')), 200);
    }
}
