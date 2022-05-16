<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'username'=>'faboma',
            'has_access' => 1,
            'password'=>'$2y$12$23cIIDCgFO/1IRfD0HYWvOPtiW0gPBJaGBIMBdrsRVHpvjiEEqsC.',
            'created_at' => new \DateTime()
        ]);
    }
}
