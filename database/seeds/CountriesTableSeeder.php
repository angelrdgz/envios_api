<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
            ['code' => "MX", 'name' => 'México'],
            ['code' => "DE", 'name' => 'Alemania'],
            ['code' => "AU", 'name' => 'Australia'],
            ['code' => "AR", 'name' => 'Argentina'],
            ['code' => "BO", 'name' => 'Bolivia'],
            ['code' => "BR", 'name' => 'Brasil'],
            ['code' => "CA", 'name' => 'Canadá'],
            ['code' => "CL", 'name' => 'Chile'],
            ['code' => "CN", 'name' => 'China'],
            ['code' => "CO", 'name' => 'Colombia'],
            ['code' => "KR", 'name' => 'Corea del Sur'],
            ['code' => "EC", 'name' => 'Ecuador'],
            ['code' => "ES", 'name' => 'España'],
            ['code' => "US", 'name' => 'Estados Unidos'],
            ['code' => "IL", 'name' => 'Israel'],
            ['code' => "NZ", 'name' => 'Nueva Zelanda'],
            ['code' => "JP", 'name' => 'Japón'],
            ['code' => "FR", 'name' => 'Francia'],
            ['code' => "GR", 'name' => 'Grecia'],
            ['code' => "GT", 'name' => 'Guatemala'],
            ['code' => "PA", 'name' => 'Panama'],
            ['code' => "PE", 'name' => 'Perú'],
            ['code' => "PR", 'name' => 'Puerto Rico'],
            ['code' => "GB", 'name' => 'Reino Unido'],
            ['code' => "DO", 'name' => 'República Dominicana'],
            ['code' => "RU", 'name' => 'Rusia'],
            ['code' => "CH", 'name' => 'Suiza'],
            ['code' => "UY", 'name' => 'Uruguay']
        ]);
    }
}
