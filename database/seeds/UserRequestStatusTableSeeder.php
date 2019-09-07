<?php

use Illuminate\Database\Seeder;
use App\UserRequestStatus;

class UserRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $request_status = new UserRequestStatus();
        $request_status->status = "Open";
        $request_status->save();

        $request_status = new UserRequestStatus();
        $request_status->status = "In progress";
        $request_status->save();
        
        $request_status = new UserRequestStatus();
        $request_status->status = "Completed";
        $request_status->save();
        
        $request_status = new UserRequestStatus();
        $request_status->status = "Cancel";
        $request_status->save();                        
    }
}
