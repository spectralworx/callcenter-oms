<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CallCenterUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'callcenter@local'],
            [
                'name' => 'Call Center',
                'pin_hash' => Hash::make('2026'),
                // password može ostati null ako si ga izbacio iz auth flow-a
                // ako users tabela zahteva password NOT NULL, onda stavi random:
                // 'password' => Hash::make(str()->random(32)),
            ]
        );
    }
}