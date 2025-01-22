<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Exception;
use Illuminate\Http\Request;

class SuscriptionUserController extends Controller
{
    public function getRucsInfo(Request $request)
    {
        try {
            $transaction = Subscription::firstWhere('tenant_id', $request->header('X-Tenant'));


            return response()->json(
                $transaction,
                200
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
