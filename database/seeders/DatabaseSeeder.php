<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Enrollment;
use App\Models\Professor;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SubjectSeeder::class,
            DepartmentSeeder::class,
            DeptHeadSeeder::class,
            ProgramSeeder::class,
            UserSeeder::class,
            StudentSeeder::class,
            AcademicYearsSeeder::class,
            // EnrolledSubjectsSeeder::class,
            ProfessorSeeder::class,
            AdminUserSeeder::class,
            ProgramSubjectSeeder::class,
            ApptMgmtSettingsTableSeeder::class,
            ServicesTableSeeder::class,
            SectionTypesSeeder::class,
            SectionsSeeder::class,
            SectionSubjectSeeder::class,
            SectionSubjectSchedulesSeeder::class,
            EnrollmentSeeder::class,
            ApiSeeder::class,
        ]);
    }
}
