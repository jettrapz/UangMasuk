<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = Hash::make('admin123');
        $superadminPassword = Hash::make('super123');

        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@ledger.local',
            'password' => $adminPassword,
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@ledger.local',
            'password' => $superadminPassword,
            'role' => 'superadmin',
        ]);
    }
}
