<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create(['email' => 'admin@email.com',
                      'password' => 'password',
                      'firstname' => 'Admin',
                      'middlename' => '',
                      'lastname' => 'Admin',
                      'role' => 'admin']);

        User::create(['email' => 'student@email.com',
                      'password' => 'password',
                      'firstname' => 'Student',
                      'middlename' => '',
                      'lastname' => 'Student',
                      'role' => 'student']);

        User::create(['email' => 'teacher@email.com',
                      'password' => 'password',
                      'firstname' => 'Teacher',
                      'middlename' => '',
                      'lastname' => 'Teacher',
                      'role' => 'teacher']);

    }
}
