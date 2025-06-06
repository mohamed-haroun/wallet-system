<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            [
                'name' => 'super_admin',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'support',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert or update user types
        foreach ($userTypes as $type) {
            UserType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('User types seeded successfully!');
    }
}
