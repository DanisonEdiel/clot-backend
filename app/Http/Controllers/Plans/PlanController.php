<?php

namespace App\Http\Controllers\Plans;

use App\Exceptions\MessageExceptions;
use App\Models\Plan;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Throwable;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::where('custom', false)->where('is_started', false)->orderBy('price', 'asc')->get();
        return response()->json(['plans' => $plans], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $plan = new Plan($request->all());
            $plan->save();
            return response()->json(['success' => $plan], 200);

        }catch (\Exception $e){
            throw new MessageExceptions([
                'Error al crear plan: '. $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $plan = Subscription::find($id);

            return response()->json([
                'plan' => $plan,
            ]);
        } catch (\Throwable $th) {
            throw new MessageExceptions([
                'Plan not found!',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            throw new MessageExceptions([
                'Plan not found!',
            ]);
        }

        try {
            $plan->update($request->all());
            return response()->json(['success' => $plan], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error updating plan.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $plans = Plan::find($id);
            return response()->json(['plans' => $plans], 200);

        }catch (\Throwable $th) {
            throw new MessageExceptions([
                'Error deleting plan!',
            ]);
        }
    }


}
