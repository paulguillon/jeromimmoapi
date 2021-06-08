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
        DB::table('users')->insert([
            'lastnameUser' => Str::random(10),
            'firstnameUser' => Str::random(10) . '@gmail.com',
            'emailUser' => Str::random(10),
            'passwordUser' => Hash::make('password'),
            'idRoleUser' => 1,
            'created_by' => '1',
            'updated_by' => '1'
        ]);
    }
}
