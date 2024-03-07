<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class CS_SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $csvFilePath = database_path('seeds/sample_partial_cs_subj_seeder.csv');
        $csvFilePath = database_path('seeds/seeder_subject_final.csv');

        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Subject::create([
                'subject_code' => $record['subject_code'],
                'subject_name' => $record['subject_name'],
                'subject_description' => $record['subject_name'],
                'units_lec' => $record['units_lec'],
                'units_lab' => $record['units_lab'],
                'prerequisite_1' => $record['prerequisite_1'],
                'prerequisite_2' => $record['prerequisite_2'],
                'prerequisite_3' => $record['prerequisite_3'],
            ]);
        }
    }
}
