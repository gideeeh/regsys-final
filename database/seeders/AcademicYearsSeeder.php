<?php

namespace Database\Seeders;

use App\Models\Academic_Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AcademicYearsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $jsonFilePath = database_path('seeds/academic_years.json');
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            Academic_Year::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
