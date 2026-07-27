<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = DB::table('states')->insert([
            array('id' => '2', 'name' => 'Andhra Pradesh', 'region_id' => '4', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '3', 'name' => 'Arunachal Pradesh', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '4', 'name' => 'Assam', 'region_id' => '2', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '5', 'name' => 'Bihar', 'region_id' => '2', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '6', 'name' => 'Chandigarh', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '7', 'name' => 'Chhattisgarh', 'region_id' => '3', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '8', 'name' => 'Dadra & Nagar Haveli', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '9', 'name' => 'Daman & Diu', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '10', 'name' => 'Delhi', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '11', 'name' => 'Goa', 'region_id' => '3', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '12', 'name' => 'Gujarat', 'region_id' => '3', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '13', 'name' => 'Haryana', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '14', 'name' => 'Himachal Pradesh', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '15', 'name' => 'Jammu & Kashmir', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '16', 'name' => 'Jharkhand', 'region_id' => '2', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '17', 'name' => 'Karnataka', 'region_id' => '4', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '18', 'name' => 'Kerala', 'region_id' => '4', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '19', 'name' => 'Lakshadweep', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '20', 'name' => 'Madhya Pradesh', 'region_id' => '3', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '21', 'name' => 'Maharashtra', 'region_id' => '3', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '22', 'name' => 'Manipur', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '23', 'name' => 'Meghalaya', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '24', 'name' => 'Mizoram', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '25', 'name' => 'Nagaland', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '26', 'name' => 'Odisha', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '27', 'name' => 'Puducherry', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '28', 'name' => 'Punjab', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '29', 'name' => 'Rajasthan', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '30', 'name' => 'Sikkim', 'region_id' => '2', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '31', 'name' => 'Tamil Nadu', 'region_id' => '4', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '32', 'name' => 'Telangana', 'region_id' => '4', 'created_at' => NULL, 'updated_at' => NULL),
            // array('id' => '33', 'name' => 'Tripura', 'region_id' => 'NULL', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '34', 'name' => 'Uttar Pradesh', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '35', 'name' => 'Uttarakhand', 'region_id' => '1', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '36', 'name' => 'West Bengal', 'region_id' => '3', 'created_at' => NULL, 'updated_at' => NULL)
        ]);
    }
}
