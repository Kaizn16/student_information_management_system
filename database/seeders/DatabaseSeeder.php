<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);
        $this->call(StrandsTableSeeder::class);
        $this->call(SubjectsTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(MunicipalitiesTableSeeder::class);
        $this->call(BarangaysTableSeeder::class);
        
        User::create([
            'username' => 'administrator',
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
        ]);
    }
}
