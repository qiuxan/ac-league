<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Member;
use App\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Manager',
            'email' => 'admin@oz-manufacturer.org',
            'password' => bcrypt('123456')
        ]);
        $member_user = User::create([
            'name' => 'Member',
            'email' => 'member@oz-manufacturer.org',
            'password' => bcrypt('123456')
        ]);

        $member = Member::create([
            'company_en' => 'AUTB',
            'company_cn' => 'AUTB',
            'phone' => '+61395629442',
            'company_email' => 'admin@autb.com.au',
            'website' => 'https://www.autb.com.au' ,
            'country_en' => 'Australia',
            'country_cn' => 'Australia',
            'status' => '1',
            'user_id' => '2',
            'created_by' => '1'
        ]);

        User::create([
            'name' => 'Staff',
            'email' => 'staff@oz-manufacturer.org',
            'password' => bcrypt('123456')
        ]);
    }
}
