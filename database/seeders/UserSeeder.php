<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->updateOrInsert(
            ['email' => env('SEED_USER_EMAIL')],
            [
                'name' => env('SEED_USER_NAME'),
                'email' => env('SEED_USER_EMAIL'),
                'password' => Hash::make(env('SEED_USER_PASSWORD')),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );        
    }
}
