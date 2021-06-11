<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            // Value one
            DB::table('userdata')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyUserData' => 'Birthday',
                'valueUserData' => $faker->dateTimeThisCentury($max = '2002')->format('d-m-Y'),
                'idUser' => $i
            ]);
            // Value two
            DB::table('userData')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyUserData' => 'PhoneNumber',
                'valueUserData' => $faker->numerify('06 ## ## ## ##'),
                'idUser' => $i
            ]);
            // Value three
            DB::table('userData')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyUserData' => 'PostalAddress',
                'valueUserData' => $faker->address(),
                'idUser' => $i
            ]);
        }
    }
}
