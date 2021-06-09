<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PropertyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');

        foreach (range(1, 10) as $index) {
            DB::table('property')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'typeProperty' => $faker->randomElement($array= array ('Maison','Appartement','Terrain','Garage')),
                'priceProperty' => $faker->numberBetween($min = 40000, $max = 750000),
                'zipCodeProperty' => $faker->numerify('#####'),
                'cityProperty' => $faker->city()
            ]);
        }

    }
}
