<?php

namespace Database\Seeders;

use App\Models\SectionSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class SectionSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_section_subject_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            SectionSubject::create([
                'section_id' => $record['section_id'],
                'subject_id' => $record['subject_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
        }
    }
}
