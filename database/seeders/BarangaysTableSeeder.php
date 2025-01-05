<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $csvFile = storage_path('app/table_barangay.csv');

        if (($handle = fopen($csvFile, 'r')) !== false) {
            fgetcsv($handle, 5000, ',');
            while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                DB::table('barangays')->insert([
                    'barangay_id' => $row[0],
                    'municipality_id' => $row[1],
                    'barangay_name' => $row[2],
                ]);
            }

            fclose($handle);
        } else {
            echo "Error: Unable to open the CSV file.";
        }
    }
}
