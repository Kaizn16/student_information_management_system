<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ["subject_code" => "ENG101", "subject_title" => "Oral Communication"],
            ["subject_code" => "ENG102", "subject_title" => "Reading and Writing"],
            ["subject_code" => "FIL103", "subject_title" => "Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino"],
            ["subject_code" => "FIL104", "subject_title" => "Pagbasa at Pagsusuri ng Iba’t Ibang Teksto Tungo sa Pananaliksik"],
            ["subject_code" => "MATH105", "subject_title" => "General Mathematics"],
            ["subject_code" => "MATH106", "subject_title" => "Statistics and Probability"],
            ["subject_code" => "SCI107", "subject_title" => "Earth and Life Science"],
            ["subject_code" => "SCI108", "subject_title" => "Physical Science"],
            ["subject_code" => "SOC109", "subject_title" => "Understanding Culture, Society, and Politics"],
            ["subject_code" => "PEH110", "subject_title" => "Physical Education and Health"],
            ["subject_code" => "ICT111", "subject_title" => "Empowerment Technologies"],
            ["subject_code" => "PHI112", "subject_title" => "Introduction to the Philosophy of the Human Person"],
            ["subject_code" => "RVE113", "subject_title" => "Media and Information Literacy"]
        ];
        
        DB::table("subjects")->insert($subjects);
    }
}
