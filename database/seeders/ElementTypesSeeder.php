<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ElementTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all element types
        DB::table('element_types')->delete();
        // Insert element types
        DB::table('element_types')->updateOrInsert(
            ['name' => 'password'],
            ['created_at' => now(), 'updated_at' => now()]
        );        
        DB::table('element_types')->updateOrInsert(
            ['name' => 'text'],
            ['created_at' => now(), 'updated_at' => now()]
        );        
        DB::table('element_types')->updateOrInsert(
            ['name' => 'file'],
            ['created_at' => now(), 'updated_at' => now()]
        );        
        DB::table('element_types')->updateOrInsert(
            ['name' => 'folder'],
            ['created_at' => now(), 'updated_at' => now()]
        );        
    }
}
