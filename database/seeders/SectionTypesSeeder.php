<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('section_types')->insert([
            'section_type' => 'block',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('section_types')->insert([
            'section_type' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
