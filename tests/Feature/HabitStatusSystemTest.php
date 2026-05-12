<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitStatusSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_habit_toggle_updates_status_without_reload_response(): void
    {
        $user = User::factory()->create();
        $habit = Habit::create([
            'user_id' => $user->id,
            'title' => 'Read',
            'description' => 'Read 20 pages',
            'frequency' => 'daily',
            'frequency_type' => 'daily',
            'target_per_day' => 1,
        ]);

        $response = $this->actingAs($user)->postJson(route('habits.toggle'), [
            'habit_id' => $habit->id,
            'date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'completed',
                'count' => 1,
            ]);

        $this->assertTrue(
            HabitLog::query()
                ->where('habit_id', $habit->id)
                ->whereDate('date', now()->toDateString())
                ->where('status', 'completed')
                ->exists()
        );
    }

    public function test_habit_toggle_can_return_to_pending(): void
    {
        $user = User::factory()->create();
        $habit = Habit::create([
            'user_id' => $user->id,
            'title' => 'Stretch',
            'description' => null,
            'frequency' => 'daily',
            'frequency_type' => 'daily',
            'target_per_day' => 1,
        ]);

        HabitLog::create([
            'habit_id' => $habit->id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'count' => 1,
        ]);

        $response = $this->actingAs($user)->postJson(route('habits.toggle'), [
            'habit_id' => $habit->id,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'count' => 0,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'pending',
                'count' => 0,
            ]);

        $this->assertTrue(
            HabitLog::query()
                ->where('habit_id', $habit->id)
                ->whereDate('date', now()->toDateString())
                ->where('status', 'pending')
                ->where('count', 0)
                ->exists()
        );
    }

    public function test_mark_missed_command_marks_incomplete_daily_habits(): void
    {
        $user = User::factory()->create();
        $habit = Habit::create([
            'user_id' => $user->id,
            'title' => 'Meditate',
            'description' => null,
            'frequency' => 'daily',
            'frequency_type' => 'daily',
            'target_per_day' => 1,
        ]);

        $date = now()->subDay()->toDateString();

        $this->artisan('habits:mark-missed', ['--date' => $date])
            ->expectsOutput("Marked 1 habit logs as missed for {$date}.")
            ->assertSuccessful();

        $this->assertTrue(
            HabitLog::query()
                ->where('habit_id', $habit->id)
                ->whereDate('date', $date)
                ->where('status', 'missed')
                ->where('count', 0)
                ->exists()
        );
    }
}
