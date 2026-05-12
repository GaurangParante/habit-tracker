<?php

namespace App\Console\Commands;

use App\Models\HabitLog;
use App\Models\User;
use App\Services\HabitAnalyticsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkMissedHabits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'habits:mark-missed {--date= : The date to evaluate in Y-m-d format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark uncompleted habit logs as missed for the selected day';

    /**
     * Execute the console command.
     */
    public function handle(HabitAnalyticsService $analytics): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $updated = 0;

        User::query()->with('habits')->chunk(100, function ($users) use ($analytics, $date, &$updated) {
            foreach ($users as $user) {
                foreach ($user->habits as $habit) {
                    if (! $analytics->isDateEligibleForStatus($habit, $date)) {
                        continue;
                    }

                    $log = HabitLog::query()
                        ->where('habit_id', $habit->id)
                        ->whereDate('date', $date->toDateString())
                        ->first() ?? new HabitLog([
                            'habit_id' => $habit->id,
                            'date' => $date->toDateString(),
                        ]);

                    if ($analytics->resolveStatusForDate($habit, $log->exists ? $log : null, $date) === 'completed') {
                        continue;
                    }

                    if ($log->exists && $log->status === 'missed') {
                        continue;
                    }

                    $log->count = $log->count ?? 0;
                    $log->status = 'missed';
                    $log->save();

                    $analytics->updateHabitStreaks($habit);
                    $updated++;
                }
            }
        });

        $this->info("Marked {$updated} habit logs as missed for {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
