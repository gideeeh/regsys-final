<?php

namespace Database\Seeders;

use App\Models\Program_Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use League\Csv\Reader;

class ProgramSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $jsonFilePath = database_path('seeds/program_subjects.json');
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            Program_Subject::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
