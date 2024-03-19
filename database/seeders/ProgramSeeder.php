<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_program_seed.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            Program::create([
                'program_code' => $record['program_code'],
                'program_name' => $record['program_name'],
                'program_desc' => $record['program_desc'],
                'degree_type' => $record['degree_type'],
                'dept_id' => $record['dept_id'],
                'program_coordinator' => $record['program_coordinator'],
                'total_units' => $record['total_units'],
                'status' => $record['status'],
                'created_at' => now(),
                'updated_at' => now(),
                'program_major' => $record['program_major'],
            ]); 
        }
    }
}
