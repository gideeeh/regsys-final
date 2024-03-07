<?php

namespace Database\Seeders;

use App\Models\Section_Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class SectionScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_section_schedule_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Section_Schedule::create([
                'section_id' => $record['section_id'],
                'prof_id' => $record['prof_id'],
                'class_day' => $record['class_day'],
                'start_time' => $record['start_time'],
                'end_time' => $record['end_time'],
                'room' => $record['room'],
            ]);
        }
    }
}
