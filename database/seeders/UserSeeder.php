<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Italo',
            'email' => 'italodigitalfnix@gmail.com',
            'password' => 'Kimura1020$',
            'username' => 'italoc7',
            'saldo' => 0,
            'cargo' => 'super',
            'status' => 1,
        ]);
    }
}
