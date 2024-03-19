<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use League\Csv\Reader;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFilePath = database_path('seeds/sample_user_seeder.csv');
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        foreach($csv->getRecords() as $offset=>$record)
        {
            $role = $record['role'];
            $password = '55Changemenow99';
            if($role === 'admin')
            {
                $role = 'admin';
                $password = '22Northgate66';
            }
            User::create([
                'first_name' => $record['first_name'],
                'last_name' => $record['last_name'],
                'email' => $record['email'],
                'email_verified_at' => now(),
                'password' => $password,
                'role' => $role,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
        }
    }
}
