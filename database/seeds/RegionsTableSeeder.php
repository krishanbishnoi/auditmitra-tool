<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = DB::table('regions')->insert([
            array('id' => '1', 'name' => 'North'),
            array('id' => '2', 'name' => 'East'),
            array('id' => '3', 'name' => 'West'),
            array('id' => '4', 'name' => 'South'),
        ]);
    }
}
