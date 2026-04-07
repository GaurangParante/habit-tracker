<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::query()->upsert([
            [
                'name' => 'Beginner',
                'type' => 'streak',
                'threshold' => 7,
                'description' => 'Complete a 7-day streak.',
                'badge' => 'badge-beginner',
            ],
            [
                'name' => 'Pro',
                'type' => 'streak',
                'threshold' => 30,
                'description' => 'Complete a 30-day streak.',
                'badge' => 'badge-pro',
            ],
            [
                'name' => 'Master',
                'type' => 'streak',
                'threshold' => 100,
                'description' => 'Complete a 100-day streak.',
                'badge' => 'badge-master',
            ],
            [
                'name' => 'Finisher',
                'type' => 'completion',
                'threshold' => 50,
                'description' => 'Log 50 total completions.',
                'badge' => 'badge-finisher',
            ],
            [
                'name' => 'Committed',
                'type' => 'completion',
                'threshold' => 200,
                'description' => 'Log 200 total completions.',
                'badge' => 'badge-committed',
            ],
        ], ['name'], ['type', 'threshold', 'description', 'badge']);
    }
}
