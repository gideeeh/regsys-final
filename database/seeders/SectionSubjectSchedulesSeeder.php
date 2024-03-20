<?php

namespace Database\Seeders;

use App\Models\SectionSubjectSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SectionSubjectSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $jsonFilePath = database_path('seeds/section_subject_schedules.json');
        
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            SectionSubjectSchedule::create([
                'sec_sub_id' => $item['sec_sub_id'],
                'prof_id' => $item['prof_id'],
                'class_days_f2f' => json_encode($item['class_days_f2f']),
                'class_days_online' => json_encode($item['class_days_online']),
                'start_time_f2f' => $item['start_time_f2f'],
                'end_time_f2f' => $item['end_time_f2f'],
                'start_time_online' => $item['start_time_online'],
                'end_time_online' => $item['end_time_online'],
                'room' => $item['room'],
                'class_limit' => $item['class_limit'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ]);
        }
    }
}
