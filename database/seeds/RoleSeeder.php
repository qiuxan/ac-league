<?php

use App\Constant;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'add production partners']);
        Permission::create(['name' => 'edit production partners']);

        $role1 = Role::create(['name' => Constant::ADMIN]);
        $role2 = Role::create(['name' => Constant::STAFF]);
        $role3 = Role::create(['name' => Constant::MEMBER]);
        $role1->givePermissionTo('add production partners');

    }
}
