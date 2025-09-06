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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'mohammad',
            'password' => Hash::make('123456789')
        ]);
        $this->call([
        LocationsTableSeeder::class,
        BusesTableSeeder::class,
        DriversTableSeeder::class,
        StudentsTableSeeder::class,
        PresentationsTableSeeder::class,
        RetreatsTableSeeder::class,
        WagesTableSeeder::class,
        AdminsTableSeeder::class,
    ]);
    }
}