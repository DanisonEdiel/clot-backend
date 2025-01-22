<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public Config $config;

    public function update(Request $request)
    {
        $this->config = Config::where('id', $request->id)->first();
        $this->config->update($request->all());
        $this->config->documents = json_encode($request->documents);
        return response()->json($this->config, 200);
    }

    public function show(Request $request)
    {
        $this->searchConfig($request->header('X-Tenant'));
        return response()->json($this->config, 200);
    }

    public function searchConfig($tenantId)
    {
        $this->config = Config::where('tenant_id', $tenantId)->first();
    }
}
