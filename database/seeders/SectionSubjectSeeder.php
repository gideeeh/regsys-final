<?php

namespace Database\Seeders;

use App\Models\SectionSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SectionSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $jsonFilePath = database_path('seeds/section_subjects.json');
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            SectionSubject::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
