<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['id' => 1, 'title' => 'uploader',  'created_at' => new \DateTime()],
            ['id' => 2, 'title' => 'viewer', 'created_at' => new \DateTime()],
        ]);
    }
}
