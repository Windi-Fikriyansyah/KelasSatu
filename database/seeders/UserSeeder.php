<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.com.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Owner User
        User::create([
            'name' => 'School Owner',
            'email' => 'owner@sekolah.com.id',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        // Create Guru User (optional)
        User::create([
            'name' => 'Guru Contoh',
            'email' => 'guru@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        // Create Siswa User (optional)
        User::create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'email_verified_at' => now(),
        ]);
    }
}
