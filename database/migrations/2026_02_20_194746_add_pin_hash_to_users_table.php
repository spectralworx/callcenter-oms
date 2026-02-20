<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CallCenterUserSeeder extends Seeder
{
    public function run(): void
    {
        // Jedan jedini user (ako već postoji, ne dupliraj)
        User::query()->updateOrCreate(
            ['email' => 'callcenter@local'],
            [
                'name' => 'Call Center',
                'password' => Hash::make(str()->random(40)), // ne koristi se
                'pin_hash' => Hash::make('2026'),
            ]
        );
    }
}