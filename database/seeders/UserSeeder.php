<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('159951'),
                'role' => User::ROLE_MAIN_ADMIN,
                'email_verified_at' => now(),
            ],
        );
    }
}
