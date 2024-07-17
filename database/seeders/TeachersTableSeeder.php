<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class TeachersTableSeeder extends Seeder
{
    public function run()
    {
        Teacher::create([
            'username' => 'admin', // Your default username
            'password' => Hash::make('admin@123'), // Your default password (hashed)
        ]);
    }
}
