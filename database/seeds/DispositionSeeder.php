<?php

use Illuminate\Database\Seeder;
use App\Disposition;

class DispositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispostion = new Disposition();
        $dispostion->disposition = "Active";
        $dispostion->save();

        $dispostion = new Disposition();
        $dispostion->disposition = "Transit";
        $dispostion->save();

        $dispostion = new Disposition();
        $dispostion->disposition = "Selling";
        $dispostion->save();

        $dispostion = new Disposition();
        $dispostion->disposition = "Sold";
        $dispostion->save();

        $dispostion = new Disposition();
        $dispostion->disposition = "Recalled";
        $dispostion->save();

        $dispostion = new Disposition();
        $dispostion->disposition = "Blacklisted";
        $dispostion->save();
    }
}