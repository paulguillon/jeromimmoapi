<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'roleName' => 'admin',
            'created_by' => 1,
            'updated_by' => 1
        ]);
        DB::table('roles')->insert([
            'roleName' => 'superadmin',
            'created_by' => 1,
            'updated_by' => 1
        ]);
        DB::table('roles')->insert([
            'roleName' => 'client',
            'created_by' => 1,
            'updated_by' => 1
        ]);
        DB::table('roles')->insert([
            'roleName' => 'agent',
            'created_by' => 1,
            'updated_by' => 1
        ]);
        
        // DB::table('roles')->insert([
        //     'roleName' => Str::random(10),
        //     'created_by' => 1,
        //     'updated_by' => 1
        // ]);
    }
}
