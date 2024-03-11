<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApptMgmtSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('appt_mgmt_settings')->insert([
            'request_limit' => 10,
            'buffer_time_minutes' => 15,
            'am_availability_start' => '08:00',
            'am_availability_end' => '12:00',
            'pm_availability_start' => '13:00',
            'pm_availability_end' => '17:00',
            'available_schedules' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'received_request_reply' => 'Your request has been received and is being processed.',
        ]);
    }
}
