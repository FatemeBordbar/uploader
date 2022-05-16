<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntitiesSeeder extends Seeder
{
    public function run()
    {
        DB::table('entities')->insert([
            ['id' => 1, 'caption' => 'articles photo', 'entity_set' => 'articles', 'created_at' => new \DateTime()],
            ['id' => 2, 'caption' => 'articles video', 'entity_set' => 'articles', 'created_at' => new \DateTime()],
            ['id' => 3, 'caption' => 'articles pdf', 'entity_set' => 'articles', 'created_at' => new \DateTime()],
           ]);
    }
}
