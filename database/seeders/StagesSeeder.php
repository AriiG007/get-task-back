<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stage;

class StagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            ['name' => 'Back Log', 'description' => 'Task in backlog', 'order' => 1],
            ['name' => 'To Do', 'description' => 'Task to be done and ready to start', 'order' => 2],
            ['name' => 'In Progress', 'description' => 'Task in progress', 'order' => 3],
            ['name' => 'Review', 'description' => 'Task under review', 'order' => 4],
            ['name' => 'Done', 'description' => 'Task completed', 'order' => 5]
        ];


        foreach ($stages as $stage) {
            Stage::updateOrCreate($stage);
        }
    }
}
