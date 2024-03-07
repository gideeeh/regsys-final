<?php

namespace Database\Seeders;

use App\Models\Academic_Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_academic_years_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Academic_Year::create([
                'acad_year' => $record['acad_year'],
                'term_1_start' => $record['term_1_start'],
                'term_1_end' => $record['term_1_end'],
                'term_2_start' => $record['term_2_start'],
                'term_2_end' => $record['term_2_end'],
                'term_3_start' => $record['term_3_start'],
                'term_3_end' => $record['term_3_end'],
            ]);
        }
    }
}
