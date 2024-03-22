<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $jsonFilePath = database_path('seeds/api_keys.json');
        $data = json_decode(File::get($jsonFilePath), true);
        
        foreach ($data as $item) {
            ApiKey::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
