<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\LoginRequest;
use App\Interfaces\admin\AdminRepository;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private AdminRepository $repository;

    public function __construct(AdminRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login(LoginRequest $request){
        return response()->json($this->repository->login($request->email, $request->password),200);
    }

    public function dashboard()
    {
        return response()->json($this->repository->dashboard());
    }

    public function showCompany(Request $request, string $tenantId)
    {
        return response()->json($this->repository->showCompany($tenantId));
    }

    public function addAdmin(Request $request){
        return response()->json($this->repository->addNewAdmin($request->email, $request->name, $request->adminId),200);
    }
}
