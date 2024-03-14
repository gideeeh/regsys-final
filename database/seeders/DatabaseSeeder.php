<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Enrollment;
use App\Models\Professor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CS_SubjectSeeder::class,
            DepartmentSeeder::class,
            DeptHeadSeeder::class,
            // ProgramSeeder::class,
            // StudentSeeder::class,
            // EnrollmentSeeder::class,
            AcademicYearSeeder::class,
            // SectionSeeder::class,
            // EnrolledSubjectsSeeder::class,
            ProfessorSeeder::class,
            // SectionScheduleSeeder::class,
            AdminUserSeeder::class,
            // ProgramSubjectSeeder::class,
            ApptMgmtSettingsTableSeeder::class,
        ]);
    }
}
