<?php

namespace App\Http\Controllers;

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

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
