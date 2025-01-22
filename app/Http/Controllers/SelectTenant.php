<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageExceptions;
use App\Models\Ruc;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SelectTenant extends Controller
{
    public function selectRuc(Request $request)
    {
        $tenants = Ruc::find($request->tenantId);

        $activeTokens = DB::table('personal_access_tokens')->where('tenant_id', $request->tenantId)->count();

        if ($activeTokens >= $tenants->subscription->usersOnline) {
            throw new MessageExceptions([
                'La empresa alcanzo el limite de sessiones activas!',
            ]);

        } else {
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $request->user['id'])
                ->delete();

            return response()->json(['ruc' => $tenants]);;
        }
    }
}
