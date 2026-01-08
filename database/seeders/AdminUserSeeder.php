<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'oktovaaaaa@gmail.com'], // email login
            [
                'name'     => 'Admin',
                'password' => Hash::make('popopo808080'), // password login
            ]
        );
    }
}
