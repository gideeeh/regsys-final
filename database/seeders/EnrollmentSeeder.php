<?php

namespace Database\Seeders;

use App\Models\Enrolled_Subject;
use App\Models\Enrollment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFilePathEnrollment = database_path('seeds/enrollments.json');
        $jsonFilePathEnrolledSubjects = database_path('seeds/enrolled_subjects.json');
        
        $enrollmentData = json_decode(File::get($jsonFilePathEnrollment), true);
        $enrolledSubjectsData = json_decode(File::get($jsonFilePathEnrolledSubjects), true);
        
        foreach ($enrollmentData as $item) {
            Enrollment::create([
                'student_id' => $item['student_id'],
                'program_id' => $item['program_id'],
                'academic_year' => $item['academic_year'],
                'term' => $item['term'],
                'year_level' => $item['year_level'],
                'batch' => $item['batch'],
                'enrollment_date' => $item['enrollment_date'],
                'scholarship_type' => $item['scholarship_type'],
                'status' => $item['status'],
                'enrollment_method' => $item['enrollment_method'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'enrollment_code' => $item['enrollment_code'],
            ]);
        }

        foreach ($enrolledSubjectsData as $item) {
            Enrolled_Subject::create([
                'enrollment_id' => $item['enrollment_id'],
                'subject_id' => $item['subject_id'],
                'sec_sub_id' => $item['sec_sub_id'],
                'final_grade' => $item['final_grade'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
                'enrolledSubject_code' => $item['enrolledSubject_code'],
                'remarks' => $item['remarks'],
            ]);
        }
        
    }
}
