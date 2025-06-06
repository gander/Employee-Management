<?php

namespace Database\Seeders;

use App\Models\Employee;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Employee::factory(10)->create();

        Employee::factory()->create([
            'full_name' => 'Test Employee',
            'email' => 'test@example.com',
        ]);
    }
}
