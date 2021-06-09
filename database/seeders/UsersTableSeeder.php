<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('users')->insert([
                'lastnameUser' => $faker->lastName,
                'firstnameUser' => $faker->firstName,
                'emailUser' => $faker->unique()->email,
                'passwordUser' => Hash::make('password'),
                'idRoleUser' => $faker->numberBetween($min = 1, $max = 4),
                'created_by' => "1",
                'updated_by' => "1"
            ]);
        }

    }
}
