<?php

namespace Database\Seeders;

use App\Models\Program_Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class ProgramSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_program_subjects_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Program_Subject::create([
                'program_id' => $record['program_id'],
                'subject_id' => $record['subject_id'],
                'term' => $record['term'],
                'year' => $record['year'],
            ]); 
        }
    }
}
