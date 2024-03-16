<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('services')->insert([
            'service_name' => 'In-Person Consultation',
            'description' => 'In-person consultations at the registrar\'s office offer direct assistance with academic queries, enrollment procedures, and administrative support.',
            'isActive' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
