<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cria um usuário Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@exemplo.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Cria um usuário comum
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@exemplo.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('user');
    }
}