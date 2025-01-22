<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create([
            'name' => 'Admin',
            'access' => json_encode(['admin' => true]),
        ]);

        \App\Models\Role::create([
            'name' => 'User',
            'access' => json_encode(['admin' => false]),
        ]);
    }
}
