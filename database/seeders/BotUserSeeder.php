<?php

namespace Database\Seeders;

use App\enums\BotEnum;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BotUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Tygor',
            'email' => 'rpendebot@bbf.com.ec',
            'password' => Hash::make('204152136')
        ]);
        // 1d85833b-3f00-4d9c-a5bc-c9b972da063e

        \App\Models\Bot::create([
            'name' => 'Tygor',
            'token' => BotEnum::TYGOR->token(),
        ]);
    }
}
