<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create predictable test users for each role
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Provider User',
                'email' => 'provider@example.com',
                'password' => 'password',
                'role' => 'provider',
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'password' => 'password',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'role' => $u['role'],
                ]
            );
        }
    }
}

