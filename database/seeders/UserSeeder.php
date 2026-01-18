<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'UserID' => 'admin',
                'PasswordHash' => 'admin123',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'UserID' => 2,
                'PasswordHash' => 'doctor123',
                'role' => 'doctor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'UserID' => 3,
                'PasswordHash' => 'nurse123',
                'role' => 'nurse',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'UserID' => 4,
                'PasswordHash' => 'parent123',
                'role' => 'parent',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert or update users
        foreach ($users as $user) {
            DB::table('user')->updateOrInsert(
                ['UserID' => $user['UserID']],
                $user
            );
        }

        // Display test credentials
        $this->command->info('Test credentials created/updated:');
        $this->command->table(
            ['UserID', 'Role', 'Password'],
            [
                ['admin', 'admin', 'admin123'],
                ['2', 'doctor', 'doctor123'],
                ['3', 'nurse', 'nurse123'],
                ['4', 'parent', 'parent123'],
            ]
        );
    }
}
