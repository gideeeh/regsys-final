<?php

namespace Database\Seeders;

use App\Models\Dept_Head;
use League\Csv\Reader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeptHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_dept_heads_seed.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);
        
        foreach($csv->getRecords() as $offset=>$record) 
        {
            Dept_Head::create([
                'first_name' => $record['first_name'],
                'middle_name' => $record['middle_name'],
                'last_name' => $record['last_name'],
                'suffix' => $record['suffix'],
                'dept_id' => $record['dept_id'],
                'personal_email' => $record['personal_email'],
                'school_email' => $record['school_email'],
            ]);
        }
    }
}
