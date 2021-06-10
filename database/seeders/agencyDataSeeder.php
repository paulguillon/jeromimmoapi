<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class agencyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            // Value one
            DB::table('agencydata')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyAgencyData' => 'Rue',
                'valueAgencyData' => $faker->streetAddress(),
                'idAgency' => $i
            ]);
            // Value two
            DB::table('agencydata')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyAgencyData' => 'Téléphone',
                'valueAgencyData' => $faker->numerify('01 ## ## ## ##'),
                'idAgency' => $i
            ]);
            // Value three
            DB::table('agencydata')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyAgencyData' => 'Email',
                'valueAgencyData' => $faker->email(),
                'idAgency' => $i
            ]);
            // Value four
            DB::table('agencydata')->insert([
                'created_by' => '1',
                'updated_by' => '1',
                'keyAgencyData' => 'Site internet',
                'valueAgencyData' => $faker->domainName(),
                'idAgency' => $i
            ]);

        }
    }
}
