<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wing;
use App\Models\region;
use App\Models\Driver;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'mohammad',
            'password' => Hash::make('123456789')
        ]);
        Wing::create([
            'Name' => 'الحمد',
        ]);
        Wing::create([
            'Name' => 'البادية',
        ]);
        Wing::create([
            'Name' => 'القعيطي',
        ]);
        region::create([
            'Name' => 'القطن',
        ]);
        region::create([
            'Name' => 'عقران',
        ]);
        $wing = Wing::first();
        Driver::create([
            'Name' => 'Mohammad',
            'IDNo' => '12345678',
            'Phone' => '0771234567',
            'LicenseNo' => 'A12345',
            'Ownership' => 'ملك شخصي',
            'wing_id' => $wing->id,
            'region_id' => 1
        ]);
        // $this->call([
        //     LocationsTableSeeder::class,
        //     BusesTableSeeder::class,
        //     DriversTableSeeder::class,
        //     StudentsTableSeeder::class,
        //     PresentationsTableSeeder::class,
        //     RetreatsTableSeeder::class,
        //     WagesTableSeeder::class,
        //     AdminsTableSeeder::class,
        // ]);
    }
}