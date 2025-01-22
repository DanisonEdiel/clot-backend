<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'Básico', 'vouchers' => 50, 'rucs' => 1, "usersOnline" => 1, "price" => 0.00, "custom" => false, "is_started" => true],
            ['name' => 'Básico', 'vouchers' => 1000, 'rucs' => 1, "usersOnline" => 1, "price" => 30.00, "custom" => false, "is_started" => false],
            ['name' => 'Esencial', 'vouchers' => 1500, 'rucs' => 1, "usersOnline" => 1, "price" => 55.0, "custom" => false, "is_started" => false],
            ['name' => 'Avanzado', 'vouchers' => 2500, 'rucs' => 2, "usersOnline" => 1, "price" => 100.00, "custom" => false, "is_started" => false],
            ['name' => 'Profesional', 'vouchers' => 15000, 'rucs' => 5, "usersOnline" => 2, "price" => 250.00, "custom" => false, "is_started" => false],
            ['name' => 'Empresarial', 'vouchers' => 25000, 'rucs' => 10, "usersOnline" => 2, "price" => 400.00, "custom" => false, "is_started" => false],
        ];

        Plan::insert($plans);
    }
}
