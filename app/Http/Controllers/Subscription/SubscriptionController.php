<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public $subscription;

    private function update(Request $request)
    {
        $subscription = $this->findSubscription($request->tenantId);
        $subscription->vouchers = $request->subscription['vouchers'];
        $subscription->usersOnline = $request->subscription['usersOnline'];
        $subscription->save();
        return response()->json([$subscription], 200);
    }

    private function show(Request $request)
    {
        return response()->json([$this->findSubscription($request->tenantId)],200);
    }

    private function findSubscription($id)
    {
        return Subscription::firstWhere('tenant_id', $id);
    }
}
