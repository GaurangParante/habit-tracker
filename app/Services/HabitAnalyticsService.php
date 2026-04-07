<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class HabitAnalyticsService
{
    public function calculateStreak(Habit $habit, ?Collection $logs = null, ?Carbon $today = null): array
    {
        $today = ($today ?? Carbon::today())->startOfDay();
        $logs = $logs ?? $habit->logs()->get();
        $completed = $this->completedDateSet($habit, $logs);
        $createdAt = $habit->created_at ? $habit->created_at->copy()->startOfDay() : $today->copy()->subDays(365);

        if ($this->isTimesPerWeek($habit)) {
            return $this->weeklyStreaks($habit, $completed, $today, $createdAt);
        }

        if ($this->isMonthly($habit)) {
            return $this->monthlyStreaks($habit, $completed, $today, $createdAt);
        }

        $current = 0;
        $cursor = $today->copy();
        while ($cursor->gte($createdAt)) {
            if ($this->isScheduledDate($habit, $cursor)) {
                $dateKey = $cursor->toDateString();
                if (isset($completed[$dateKey])) {
                    $current++;
                } else {
                    break;
                }
            }
            $cursor->subDay();
        }

        $longest = 0;
        $running = 0;
        foreach (CarbonPeriod::create($createdAt, $today) as $date) {
            if (! $this->isScheduledDate($habit, $date)) {
                continue;
            }
            $dateKey = $date->toDateString();
            if (isset($completed[$dateKey])) {
                $running++;
                $longest = max($longest, $running);
            } else {
                $running = 0;
            }
        }

        return [
            'current' => $current,
            'longest' => $longest,
        ];
    }

    public function calculateScore(
        Habit $habit,
        ?Collection $logs = null,
        ?Carbon $today = null,
        ?int $daysBack = null
    ): array {
        $today = ($today ?? Carbon::today())->startOfDay();
        $start = $daysBack ? $today->copy()->subDays($daysBack - 1) : ($habit->created_at?->copy()->startOfDay() ?? $today->copy()->subDays(30));
        $logs = $logs ?? $habit->logs()->whereBetween('date', [$start->toDateString(), $today->toDateString()])->get();
        $completed = $this->completedDateSet($habit, $logs);

        $possible = 0;
        $completedCount = 0;

        if ($this->isTimesPerWeek($habit)) {
            $target = max(1, (int) ($habit->frequency_value['times'] ?? 1));
            $weeklyCounts = $this->weeklyCompletionCounts($completed, $start, $today);

            $possible = count($weeklyCounts) * $target;
            $completedCount = array_sum(array_map(fn ($count) => min($count, $target), $weeklyCounts));
        } elseif ($this->isMonthly($habit)) {
            $monthlyCounts = $this->monthlyCompletionCounts($completed, $start, $today, $habit);
            $possible = count($monthlyCounts);
            $completedCount = array_sum($monthlyCounts);
        } else {
            foreach (CarbonPeriod::create($start, $today) as $date) {
                if (! $this->isScheduledDate($habit, $date)) {
                    continue;
                }
                $possible++;
                if (isset($completed[$date->toDateString()])) {
                    $completedCount++;
                }
            }
        }

        $score = $possible > 0 ? (int) round(($completedCount / $possible) * 100) : 0;
        $label = $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : 'Needs Improvement');

        return [
            'score' => $score,
            'label' => $label,
            'completed' => $completedCount,
            'possible' => $possible,
        ];
    }

    public function getMissedDays(Habit $habit, ?Collection $logs = null, ?Carbon $today = null): int
    {
        $today = ($today ?? Carbon::today())->startOfDay();
        $weekStart = $today->copy()->startOfWeek();
        $logs = $logs ?? $habit->logs()->whereBetween('date', [$weekStart->toDateString(), $today->toDateString()])->get();
        $completed = $this->completedDateSet($habit, $logs);

        if ($this->isTimesPerWeek($habit)) {
            $target = max(1, (int) ($habit->frequency_value['times'] ?? 1));
            $completedDays = 0;
            foreach (CarbonPeriod::create($weekStart, $today) as $date) {
                if (isset($completed[$date->toDateString()])) {
                    $completedDays++;
                }
            }
            return max(0, $target - $completedDays);
        }

        $missed = 0;
        foreach (CarbonPeriod::create($weekStart, $today) as $date) {
            if (! $this->isScheduledDate($habit, $date)) {
                continue;
            }
            if (! isset($completed[$date->toDateString()])) {
                $missed++;
            }
        }

        return $missed;
    }

    public function getWeeklyPattern(User $user, ?Carbon $today = null, int $weeks = 8): array
    {
        $today = ($today ?? Carbon::today())->startOfDay();
        $start = $today->copy()->subWeeks($weeks)->startOfDay();

        $habits = $user->habits()->get();
        if ($habits->isEmpty()) {
            return [];
        }

        $habitIds = $habits->pluck('id');
        $logs = HabitLog::query()
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('date', [$start->toDateString(), $today->toDateString()])
            ->get()
            ->groupBy('habit_id');

        $completedSets = [];
        foreach ($habits as $habit) {
            $completedSets[$habit->id] = $this->completedDateSet($habit, $logs->get($habit->id, collect()));
        }

        $stats = [
            'Mon' => ['completed' => 0, 'possible' => 0],
            'Tue' => ['completed' => 0, 'possible' => 0],
            'Wed' => ['completed' => 0, 'possible' => 0],
            'Thu' => ['completed' => 0, 'possible' => 0],
            'Fri' => ['completed' => 0, 'possible' => 0],
            'Sat' => ['completed' => 0, 'possible' => 0],
            'Sun' => ['completed' => 0, 'possible' => 0],
        ];

        foreach (CarbonPeriod::create($start, $today) as $date) {
            $label = $date->format('D');
            foreach ($habits as $habit) {
                if ($this->isTimesPerWeek($habit)) {
                    $stats[$label]['possible']++;
                    if (isset($completedSets[$habit->id][$date->toDateString()])) {
                        $stats[$label]['completed']++;
                    }
                    continue;
                }

                if (! $this->isScheduledDate($habit, $date)) {
                    continue;
                }
                $stats[$label]['possible']++;
                if (isset($completedSets[$habit->id][$date->toDateString()])) {
                    $stats[$label]['completed']++;
                }
            }
        }

        $pattern = [];
        foreach ($stats as $day => $values) {
            $rate = $values['possible'] > 0 ? (int) round(($values['completed'] / $values['possible']) * 100) : 0;
            $pattern[] = [
                'day' => $day,
                'completion_rate' => $rate,
            ];
        }

        return $pattern;
    }

    public function getBestWorstHabits(User $user, ?Carbon $today = null, int $daysBack = 30): array
    {
        $today = ($today ?? Carbon::today())->startOfDay();
        $start = $today->copy()->subDays($daysBack - 1);

        $habits = $user->habits()->get();
        if ($habits->isEmpty()) {
            return ['best' => null, 'worst' => null];
        }

        $habitIds = $habits->pluck('id');
        $logsByHabit = HabitLog::query()
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('date', [$start->toDateString(), $today->toDateString()])
            ->get()
            ->groupBy('habit_id');

        $scores = [];
        foreach ($habits as $habit) {
            $score = $this->calculateScore($habit, $logsByHabit->get($habit->id, collect()), $today, $daysBack);
            $scores[] = [
                'habit' => $habit,
                'score' => $score['score'],
                'label' => $score['label'],
            ];
        }

        $best = collect($scores)->sortByDesc('score')->first();
        $worst = collect($scores)->sortBy('score')->first();

        return [
            'best' => $best,
            'worst' => $worst,
        ];
    }

    public function getHeatmapData(User $user, ?Carbon $today = null, int $daysBack = 90): array
    {
        $today = ($today ?? Carbon::today())->startOfDay();
        $start = $today->copy()->subDays($daysBack - 1);

        $habits = $user->habits()->get()->keyBy('id');
        if ($habits->isEmpty()) {
            return [];
        }

        $habitIds = $habits->keys();
        $logs = HabitLog::query()
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('date', [$start->toDateString(), $today->toDateString()])
            ->get();

        $map = [];
        foreach (CarbonPeriod::create($start, $today) as $date) {
            $map[$date->toDateString()] = 0;
        }

        foreach ($logs as $log) {
            $habit = $habits->get($log->habit_id);
            if (! $habit) {
                continue;
            }
            $completed = $this->isCompleted($habit, $log);
            if ($completed) {
                $dateKey = $log->date->toDateString();
                if (! isset($map[$dateKey])) {
                    $map[$dateKey] = 0;
                }
                $map[$dateKey]++;
            }
        }

        $data = [];
        foreach ($map as $date => $count) {
            $data[] = [
                'date' => $date,
                'count' => $count,
            ];
        }

        return $data;
    }

    public function syncAchievements(User $user): Collection
    {
        $achievements = Achievement::query()->get();
        if ($achievements->isEmpty()) {
            return collect();
        }

        $habits = $user->habits()->get();
        $habitIds = $habits->pluck('id');
        $logs = HabitLog::query()->whereIn('habit_id', $habitIds)->get();

        $longestStreak = 0;
        $totalCompletions = 0;

        foreach ($habits as $habit) {
            $habitLogs = $logs->where('habit_id', $habit->id);
            $streaks = $this->calculateStreak($habit, $habitLogs);
            $longestStreak = max($longestStreak, $streaks['longest']);

            foreach ($habitLogs as $log) {
                if ($this->isCompleted($habit, $log)) {
                    $totalCompletions++;
                }
            }
        }

        $unlocked = collect();
        foreach ($achievements as $achievement) {
            $qualified = match ($achievement->type) {
                'streak' => $longestStreak >= $achievement->threshold,
                'completion' => $totalCompletions >= $achievement->threshold,
                default => false,
            };

            if ($qualified) {
                $user->achievements()->syncWithoutDetaching([
                    $achievement->id => ['unlocked_at' => now()],
                ]);
                $unlocked->push($achievement);
            }
        }

        return $unlocked;
    }

    public function updateHabitStreaks(Habit $habit, ?Collection $logs = null): array
    {
        $streaks = $this->calculateStreak($habit, $logs);
        $habit->update([
            'current_streak' => $streaks['current'],
            'longest_streak' => max($habit->longest_streak ?? 0, $streaks['longest']),
        ]);

        return $streaks;
    }

    private function isCompleted(Habit $habit, HabitLog $log): bool
    {
        $target = max(1, (int) $habit->target_per_day);
        return (int) $log->count >= $target || (bool) $log->status;
    }

    private function completedDateSet(Habit $habit, Collection $logs): array
    {
        $completed = [];
        foreach ($logs as $log) {
            if ($this->isCompleted($habit, $log)) {
                $completed[$log->date->toDateString()] = true;
            }
        }

        return $completed;
    }

    private function isScheduledDate(Habit $habit, Carbon $date): bool
    {
        $type = $habit->frequency_type ?? $habit->frequency;
        $value = $habit->frequency_value ?? [];

        if ($type === 'days_of_week') {
            $day = strtolower($date->format('D'));
            return in_array($day, $value, true);
        }

        if ($type === 'monthly') {
            $day = $value['day'] ?? $habit->created_at?->day ?? 1;
            return (int) $date->day === (int) $day;
        }

        return true;
    }

    private function isTimesPerWeek(Habit $habit): bool
    {
        return ($habit->frequency_type ?? '') === 'times_per_week';
    }

    private function isMonthly(Habit $habit): bool
    {
        return ($habit->frequency_type ?? '') === 'monthly';
    }

    private function weeklyStreaks(Habit $habit, array $completed, Carbon $today, Carbon $start): array
    {
        $target = max(1, (int) ($habit->frequency_value['times'] ?? 1));
        $weeklyCounts = $this->weeklyCompletionCounts($completed, $start, $today);

        $weeks = array_values($weeklyCounts);
        $current = 0;
        for ($i = count($weeks) - 1; $i >= 0; $i--) {
            if ($weeks[$i] >= $target) {
                $current++;
            } else {
                break;
            }
        }

        $longest = 0;
        $running = 0;
        foreach ($weeks as $count) {
            if ($count >= $target) {
                $running++;
                $longest = max($longest, $running);
            } else {
                $running = 0;
            }
        }

        return [
            'current' => $current,
            'longest' => $longest,
        ];
    }

    private function monthlyStreaks(Habit $habit, array $completed, Carbon $today, Carbon $start): array
    {
        $monthlyCounts = $this->monthlyCompletionCounts($completed, $start, $today, $habit);
        $months = array_values($monthlyCounts);

        $current = 0;
        for ($i = count($months) - 1; $i >= 0; $i--) {
            if ($months[$i] >= 1) {
                $current++;
            } else {
                break;
            }
        }

        $longest = 0;
        $running = 0;
        foreach ($months as $count) {
            if ($count >= 1) {
                $running++;
                $longest = max($longest, $running);
            } else {
                $running = 0;
            }
        }

        return [
            'current' => $current,
            'longest' => $longest,
        ];
    }

    private function weeklyCompletionCounts(array $completed, Carbon $start, Carbon $today): array
    {
        $weeklyCounts = [];
        foreach (CarbonPeriod::create($start, $today) as $date) {
            $weekKey = $date->copy()->startOfWeek()->toDateString();
            if (! isset($weeklyCounts[$weekKey])) {
                $weeklyCounts[$weekKey] = 0;
            }
            if (isset($completed[$date->toDateString()])) {
                $weeklyCounts[$weekKey]++;
            }
        }

        return $weeklyCounts;
    }

    private function monthlyCompletionCounts(array $completed, Carbon $start, Carbon $today, Habit $habit): array
    {
        $monthlyCounts = [];
        $dayOfMonth = (int) ($habit->frequency_value['day'] ?? $habit->created_at?->day ?? 1);
        $cursor = $start->copy()->startOfMonth();
        $endMonth = $today->copy()->startOfMonth();

        while ($cursor->lte($endMonth)) {
            $scheduledDate = $cursor->copy()->day($dayOfMonth);
            $monthKey = $cursor->format('Y-m');

            if ($scheduledDate->gte($start) && $scheduledDate->lte($today)) {
                $monthlyCounts[$monthKey] = isset($completed[$scheduledDate->toDateString()]) ? 1 : 0;
            }

            $cursor->addMonth();
        }

        return $monthlyCounts;
    }
}
