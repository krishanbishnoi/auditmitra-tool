<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RegionsTableSeeder::class,
            StatesTableSeeder::class,
            CitiesTableSeeder::class,
//            CommentsTableSeeder::class,
        ]);
        // $this->call(UsersTableSeeder::class);
    }
}
