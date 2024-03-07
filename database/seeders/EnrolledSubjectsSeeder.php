<?php

namespace Database\Seeders;

use App\Models\Enrolled_Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class EnrolledSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_enrolled_subjects_seeder_v2.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Enrolled_Subject::create([
                'enrollment_id' => $record['enrollment_id'],
                'subject_id' => $record['subject_id'],
                'section_id' => $record['section_id'],
                'final_grade' => $record['final_grade'] === '' || $record['final_grade'] === 'NULL' ? null : $record['final_grade'],
            ]);
        }
        
    }
}
