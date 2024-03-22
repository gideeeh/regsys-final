<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use League\Csv\Reader;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $jsonFilePathAdmins = database_path('seeds/users.json');
        $admins = json_decode(File::get($jsonFilePathAdmins), true);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($admins as $item) {
            User::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $jsonFilePathUsers = database_path('seeds/admin_users.json');
        $users = json_decode(File::get($jsonFilePathUsers), true);
        
        foreach ($users as $item) {
            User::create($item);
        }
        

        
    }
}
