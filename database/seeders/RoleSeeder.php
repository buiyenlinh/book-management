<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = new Role();
        $role1->name = 'Super Admin';
        $role1->level = 1;
        $role1->save();

        $role2 = new Role();
        $role2->name = 'Administrator';
        $role2->level = 2;
        $role2->save();

        $role3 = new Role();    
        $role3->name = "Editor";
        $role3->level = 3;
        $role3->save();

        $role4 = new Role();    
        $role4->name = "User";
        $role4->level = 4;
        $role4->save();
    }
}
