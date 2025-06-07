<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Development Team', 'description' => 'Team responsible for software development'],
            ['name' => 'Marketing Team', 'description' => 'Team responsible for marketing strategies'],
            ['name' => 'Sales Team', 'description' => 'Team responsible for sales and customer relations'],
            ['name' => 'Support Team', 'description' => 'Team responsible for customer support and assistance'],
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(['name' => $team['name']], $team);
        }
    }
}
