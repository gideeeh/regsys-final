<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_section_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Section::create([
                'section_name' => $record['section_name'],
                'subject_id' => $record['subject_id'],
                'academic_year' => $record['academic_year'],
                'term' => $record['term'],
                'is_openedUponRequest' => $record['is_openedUponRequest'],
            ]);
        }
    }
}
