<?php

namespace Database\Seeders;

use App\Models\SectionSubjectSchedule;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SectionSubjectSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $jsonFilePath = database_path('seeds/section_subject_schedules.json');
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            SectionSubjectSchedule::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
