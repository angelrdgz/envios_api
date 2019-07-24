<?php

use Illuminate\Database\Seeder;

class LocationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('location_types')->insert([
            ['id'=>1, 'name'=>'origen'],
            ['id'=>2, 'name'=>'destination']
        ]);
    }
}
