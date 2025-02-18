<?php

namespace Database\Seeders;

use App\Models\Statuses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Statuses::updateOrCreate(
            ['code' => 'pd'],
            [
                'name' => 'pending',
                'code' => 'pd',
                'is_active' => 1
            ]);
    }
}
