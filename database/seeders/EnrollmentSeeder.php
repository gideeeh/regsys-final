<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_enrollments_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            $status = empty($record['status']) ? 'pending' : $record['status'];
            $enrollment_method = empty($record['enrollment_method']) ? 'continuing' : $record['enrollment_method'];
            Enrollment::create([
                'student_id' => $record['student_id'],
                'program_id' => $record['program_id'],
                'academic_year' => $record['academic_year'],
                'term' => $record['term'],
                'year_level' => $record['year_level'],
                'batch' => $record['batch'] ?: null,
                'enrollment_date' => $record['enrollment_date'] ?: now(),
                'scholarship_type' => $record['scholarship_type'],
                'status' => $status,
                'enrollment_method' => $enrollment_method,
            ]);
        }
    }
}
