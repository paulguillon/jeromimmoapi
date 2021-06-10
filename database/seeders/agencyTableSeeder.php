<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class AgencyTableSeeder extends Seeder
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
            DB::table('agency')->insert([
                'nameAgency' => $faker->company,
                'zipCodeAgency' => $faker->numerify('#####'),
                'cityAgency' => $faker->city(),
                'created_by' => "1",
                'updated_by' => "1"
            ]);
        }

    }
}