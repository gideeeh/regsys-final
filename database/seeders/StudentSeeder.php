<?php

namespace Database\Seeders;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $csvFilePath = database_path('seeds/sample_student_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        
        foreach($csv->getRecords() as $offset=>$record)
        {
            $file_path = 'students/'.$record['student_number'].' '.$record['last_name'].', '.$record['first_name'];
            Student::create([
                'user_id' => $record['user_id'] ?? null,
                'student_number' => $record['student_number'],
                'first_name' => $record['first_name'],
                'middle_name' => null, 
                'last_name' => $record['last_name'],
                'suffix' => null,
                'sex' => null,
                'birthdate' => null,
                'birthplace' => null,
                'civil_status' => null,
                'nationality' =>  null,
                'religion' => null,
                'phone_number' => null,
                'personal_email' => $record['personal_email'],
                'school_email' => null,
                'house_num' => null,
                'street' => null,
                'brgy' => null,
                'city_municipality' => null,
                'province' => null,
                'zipcode' => null,
                'guardian_name' => null,
                'guardian_contact' => null,
                'elementary' => null,
                'elem_yr_grad' => null,
                'jr_highschool' => null,
                'jr_hs_yr_grad' => null,
                'sr_highschool' => null,
                'sr_hs_yr_grad' => null,
                'college' => null,
                'college_year_ended' => null,
                'is_transferee' => $record['is_transferee'],
                'is_irregular' => $record['is_irregular'],
                'created_at' => now(),
                'updated_at' => now(),
                'file_path' => $file_path,
            ]);
        }
        

    }
}
