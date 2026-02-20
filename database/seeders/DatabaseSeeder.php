<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Pozivamo CallCenterUserSeeder koji je programer pripremio
        $this->call([
            CallCenterUserSeeder::class,
        ]);

        // Tvoj stari test korisnik (opciono, možeš ostaviti ili obrisati)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}