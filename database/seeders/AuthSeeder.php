<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'fihri aziz',
            'email' => 'fihriaziz@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Admin'
        ]);
    }
}
