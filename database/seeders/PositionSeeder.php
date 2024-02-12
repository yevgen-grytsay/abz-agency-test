<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positions')->insert([
            'name' => 'Security',
        ]);

        DB::table('positions')->insert([
            'name' => 'Designer',
        ]);

        DB::table('positions')->insert([
            'name' => 'Content manager',
        ]);

        DB::table('positions')->insert([
            'name' => 'Lawyer',
        ]);
    }
}
