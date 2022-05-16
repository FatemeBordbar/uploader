<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersRolesSeeder extends Seeder
{
    public function run()
    {
        DB::table('users_roles')->insert([
            ['id' => '1', 'user_id' => 1, 'role_id' => 1 ,'created_at' => new \DateTime()]
        ]);
    }
}
