<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('userdata')->insert([
            'created_by' => Str::random(10),
            'updated_by' => Str::random(10) . '@gmail.com',
            'keyUserData' => Str::random(10),
            'valueUserData' => Hash::make('password'),
            'idUser' => '1'
        ]);
    }
}
