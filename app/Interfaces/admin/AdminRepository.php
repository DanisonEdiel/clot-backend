<?php

namespace App\Interfaces\admin;

use App\Models\Deposit;
use Aws\Crypto\Polyfill\ByteArray;

interface AdminRepository
{
    public function login(string $email,string $password);
    public function logout(int $id);
    public function changePlan (Deposit $deposit, $file);
    public function showCompany(string $tenantId);
    public function addNewAdmin(string $email, string $name, int $adminId);
    public function dashboard();
}
