<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
              "region_id" => 1,
              "region_name" => "NCR",
              "region_description" => "National Capital Region",
            ],
            [
              "region_id" => 2,
              "region_name" => "CAR",
              "region_description" => "Cordillera Administrative Region",
            ],
            [
              "region_id" => 3,
              "region_name" => "Region I",
              "region_description" => "Ilocos Region",
            ],
            [
              "region_id" => 4,
              "region_name" => "Region II",
              "region_description" => "Cagayan Valley",
            ],
            [
              "region_id" => 5,
              "region_name" => "Region III",
              "region_description" => "Central Luzon",
            ],
            [
              "region_id" => 6,
              "region_name" => "Region IV-A",
              "region_description" => "CALABARZON",
            ],
            [
              "region_id" => 7,
              "region_name" => "Region IV-B",
              "region_description" => "MIMAROPA",
            ],
            [
              "region_id" => 8,
              "region_name" => "Region V",
              "region_description" => "Bicol Region",
            ],
            [
              "region_id" => 9,
              "region_name" => "Region VI",
              "region_description" => "Western Visayas",
            ],
            [
              "region_id" => 10,
              "region_name" => "Region VII",
              "region_description" => "Central Visayas",
            ],
            [
              "region_id" => 11,
              "region_name" => "Region VIII",
              "region_description" => "Eastern Visayas",
            ],
            [
              "region_id" => 12,
              "region_name" => "Region IX",
              "region_description" => "Zamboanga Peninsula",
            ],
            [
              "region_id" => 13,
              "region_name" => "Region X",
              "region_description" => "Northern Mindanao",
            ],
            [
              "region_id" => 14,
              "region_name" => "Region XI",
              "region_description" => "Davao Region",
            ],
            [
              "region_id" => 15,
              "region_name" => "Region XII",
              "region_description" => "SOCCSKSARGEN",
            ],
            [
              "region_id" => 16,
              "region_name" => "Region XIII",
              "region_description" => "CARAGA",
            ],
            [
              "region_id" => 17,
              "region_name" => "ARMM",
              "region_description" => "Autonomous Region in Muslim Mindanao",
            ]
        ];

        DB::table('regions')->insert($regions);
    }
}
