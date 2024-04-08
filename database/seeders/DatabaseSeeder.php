<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create(['username' => 'demo1', 'password' => 'skills2023d1']);
        User::create(['username' => 'demo2', 'password' => 'skills2023d2']);

        Service::create(['name' => 'ChatterBlast', 'cost_per_ms' => 0.001500]);
        Service::create(['name' => 'DreamWeaver', 'cost_per_ms' => 0.005500]);
        Service::create(['name' => 'MindReader', 'cost_per_ms' => 0.010000]);
    }
}
