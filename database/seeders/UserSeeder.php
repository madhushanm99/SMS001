<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin#12345',
                'usertype' => 'admin',
            ],
            [
                'name' => 'manager1',
                'email' => 'manager1@gmail.com',
                'password' => 'mang#12345',
                'usertype' => 'manager',
            ],
            [
                'name' => 'staff1',
                'email' => 'staff1@gmail.com',
                'password' => 'staff#12345',
                'usertype' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'usertype' => $userData['usertype'] ?? null,
                ]
            );
        }
    }
}


