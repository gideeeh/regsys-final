<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $jsonFilePath = database_path('seeds/sections.json');
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            Section::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
