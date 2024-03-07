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
            Student::create([
                'user_id' => $record['user_id'] ?: null,
                'student_number' => $record['student_number'],
                'first_name' => $record['first_name'],
                'middle_name' => $record['middle_name'],
                'last_name' => $record['last_name'],
                'suffix' => $record['suffix'],
                'sex' => $record['sex'],
                'birthdate' => Carbon::createFromFormat('m/d/Y', $record['birthdate'])->format('Y-m-d'),
                'birthplace' => $record['birthplace'],
                'civil_status' => $record['civil_status'],
                'nationality' => $record['nationality'],
                'religion' => $record['religion'],
                'phone_number' => $record['phone_number'],
                'personal_email' => $record['personal_email'],
                'school_email' => $record['school_email'],
                'house_num' => $record['house_num'],
                'street' => $record['street'],
                'brgy' => $record['brgy'],
                'city_municipality' => $record['city_municipality'],
                'province' => $record['province'],
                'zipcode' => $record['zipcode'],
                'guardian_name' => $record['guardian_name'],
                'guardian_contact' => $record['guardian_contact'],
                'elementary' => $record['elementary'],
                'elem_yr_grad' => $record['elem_yr_grad'],
                'highschool' => $record['highschool'],
                'hs_yr_grad' => $record['hs_yr_grad'],
                'college' => $record['college'] ?: null,
                'college_year_ended' => $record['college_year_ended'] ?: null,
                'is_transferee' => $record['is_transferee'] === 'true',
                'is_irregular' => $record['is_irregular'] === 'true',
            ]);
        }

    }
}
