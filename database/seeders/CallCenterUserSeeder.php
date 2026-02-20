<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CallCenterUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'callcenter@local'],
            [
                'name' => 'Call Center',
                'password' => Hash::make(str()->random(40)), // ne koristi se, samo da nije null
                'pin_hash' => Hash::make(env('CALLCENTER_PIN', '2026')),
            ]
        );
    }
}