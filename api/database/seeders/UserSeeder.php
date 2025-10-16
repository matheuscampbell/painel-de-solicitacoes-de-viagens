<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('secret123'),
                'tipo_usuario' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Usuário Padrão',
                'password' => Hash::make('secret123'),
                'tipo_usuario' => 'cliente',
                'is_active' => true,
            ]
        );
    }
}
