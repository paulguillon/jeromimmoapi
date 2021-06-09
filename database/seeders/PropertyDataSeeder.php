<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PropertyDataSeeder extends Seeder
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
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Jardin",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Sous-sol",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Cheminée",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Gardien",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Belle vue",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Balcon",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Piscine",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Ascenseur",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Rez-de-chaussée",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Terrasse",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Cave",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Orientation Sud",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Climatisation",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Meublé",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Colocation",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Stationnement",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Plain-pied",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Accessibilité PMR",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Véranda",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Alarme",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Digicode",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Interphone",
                'valuePropertyData' => $faker->boolean($chanceOfGettingTrue = 30),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Type d'achat",
                'valuePropertyData' => $faker->randomElement($array = array('Ancien', 'Neuf', 'Viager', 'Projet de construction')),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Chambres",
                'valuePropertyData' => $faker->numberBetween($min = 1, $max = 5),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Surface",
                'valuePropertyData' => $faker->numberBetween($min = 9, $max = 150) . ' m²',
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Surface terrain",
                'valuePropertyData' => $faker->numberBetween($min = 10, $max = 1500) . ' m²',
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' =>"Nombre de pièces",
                'valuePropertyData' => $faker->randomElement($array = array('1 pièce', '2 pièces', '3 pièces', '4 pièces', '5 pièces', '6 pièces et +')),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "Exposition",
                'valuePropertyData' => $faker->randomElement($array = array('Nord', 'Sud', 'Est', 'Ouest', 'Vue mer', 'Proche mer')),
                'idProperty' => $i
            ]);
            DB::table('propertyData')->insert([
                'created_by' => "1",
                'updated_by' => "1",
                'keyPropertyData' => "thumbnail",
                'valuePropertyData' => $faker->image('', 400, 300),
                'idProperty' => $i
            ]);
        }
    }
}
