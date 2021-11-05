<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->fullname = 'Bùi Yến Linh';
        $user->username = 'linhlinh';
        $user->active = 1;
        $user->password = bcrypt('linhlinh@123');
        $user->role_id = 1;
        $user->save();
    }
}
