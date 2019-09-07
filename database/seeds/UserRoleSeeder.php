<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Constant;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where(['email' => 'admin@oz-manufacturer.org'])->first();
        $admin->assignRole(Constant::ADMIN);
        $admin->givePermissionTo('edit production partners');
        $staff = User::where(['email' => 'staff@oz-manufacturer.org'])->first();
        $staff->givePermissionTo('add production partners');
        $staff->assignRole(Constant::STAFF);
        $members = User::where([
            ['email', '<>', 'admin@oz-manufacturer.org'],
            ['email', '<>', 'staff@oz-manufacturer.org']
        ])->get();
        foreach($members as $member)
        {
            $member->assignRole(Constant::MEMBER);
        }
    }
}
