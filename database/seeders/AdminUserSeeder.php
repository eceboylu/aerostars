<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_SEED_PASSWORD');

        if (blank($password)) {
            $this->command->error('ADMIN_SEED_PASSWORD is not set in .env, the admin user was not created.');

            return;
        }

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@airstars.local',
                'password' => Hash::make($password),
            ]
        );
    }
}
