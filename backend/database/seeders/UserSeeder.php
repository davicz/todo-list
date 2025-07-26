<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task; // 1. Importe o modelo Task
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@exemplo.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@exemplo.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('user');

        Task::factory()->count(15)->create([
            'user_id' => $user->id
        ]);

        Task::factory()->count(5)->create([
            'user_id' => $admin->id
        ]);
    }
}
