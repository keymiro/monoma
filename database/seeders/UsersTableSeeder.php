<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         User::create([
            'username' => 'manager',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        User::create([
            'username' => 'agent',
            'password' => Hash::make('password'),
            'role' => 'agent',
        ]);
    }
}
