<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Updated Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}