<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $strands = [
            //ACADEMIC TRACKS
            [
                'strand_name' => 'ABM',
                'strand_description' => 'Accountancy, Business and Management',
                'strand_type' => 'Academic Track',
            ],
            [
                'strand_name' => 'HUMSS',
                'strand_description' => 'Humanities and Social Sciences',
                'strand_type' => 'Academic Track',
            ],
            [
                'strand_name' => 'STEM',
                'strand_description' => 'Science, Technology, Engineering and Mathematics',
                'strand_type' => 'Academic Track',
            ],
            // TVL
            [
                'strand_name' => 'H.E',
                'strand_description' => 'H.E',
                'strand_type' => 'TVL Track',
            ],
            [
                'strand_name' => 'IA-EIM',
                'strand_description' => 'IA-EIM',
                'strand_type' => 'TVL Track',
            ],
            [
                'strand_name' => 'ICT',
                'strand_description' => 'Information and Communication Technology',
                'strand_type' => 'TVL Track',
            ],
            [
                'strand_name' => 'AFA',
                'strand_description' => 'AFA',
                'strand_type' => 'TVL Track',
            ],
        ];

        DB::table('strands')->insert($strands);
    }
}
