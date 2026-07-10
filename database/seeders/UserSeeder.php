<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed default users for local access.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@campionamenti.test'],
            [
                'name' => 'Amministratore',
                'role' => 'admin',
                'password' => Hash::make('Password123!'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'operatore@campionamenti.test'],
            [
                'name' => 'Operatore',
                'role' => 'operatore',
                'password' => Hash::make('Password123!'),
            ]
        );
    }
}
