<?php

namespace Database\Seeders;

use App\Models\Department;
use League\Csv\Reader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_dept_seed.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record) 
        {
            Department::create([
                'dept_name' => $record['dept_name'],
            ]);
        }
    }
}
