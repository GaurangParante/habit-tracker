<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $todayString = $today->toDateString();
        $user = $request->user();

        $habits = $user->habits()
            ->leftJoin('habit_logs as today_logs', function ($join) use ($todayString) {
                $join->on('habits.id', '=', 'today_logs.habit_id')
                    ->where('today_logs.date', '=', $todayString);
            })
            ->with(['logs' => function ($query) {
                $query->orderBy('date');
            }])
            ->addSelect('habits.*', DB::raw('COALESCE(today_logs.status, 0) as today_status'))
            ->orderBy('habits.created_at')
            ->get();

        $habitStats = [];

        foreach ($habits as $habit) {
            $todayCompleted = (bool) $habit->today_status;

            [$currentStreak, $maxStreak] = $this->calculateStreaks($habit, $today);

            $completedTotal = $habit->logs->where('status', true)->count();
            $progressWindowStart = $today->copy()->subDays(29);
            $completedLast30 = $habit->logs
                ->where('status', true)
                ->where('date', '>=', $progressWindowStart->toDateString())
                ->count();

            $progressPercent = (int) round(($completedLast30 / 30) * 100);

            $habitStats[$habit->id] = [
                'today_completed' => $todayCompleted,
                'current_streak' => $currentStreak,
                'max_streak' => $maxStreak,
                'completed_total' => $completedTotal,
                'progress_percent' => $progressPercent,
            ];
        }

        return view('dashboard', [
            'habits' => $habits,
            'habitStats' => $habitStats,
            'today' => $today,
        ]);
    }

    private function calculateStreaks(Habit $habit, Carbon $today): array
    {
        $completedDates = $habit->logs
            ->where('status', true)
            ->sortBy('date')
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->values()
            ->all();

        if (empty($completedDates)) {
            return [0, 0];
        }

        $completedSet = array_flip($completedDates);
        $currentStreak = 0;
        $cursor = $today->copy();

        while (isset($completedSet[$cursor->toDateString()])) {
            $currentStreak++;
            $cursor->subDay();
        }

        $maxStreak = 0;
        $running = 0;
        $previous = null;

        foreach ($completedDates as $dateString) {
            $date = Carbon::parse($dateString);

            if ($previous && $previous->diffInDays($date) === 1) {
                $running++;
            } else {
                $running = 1;
            }

            $maxStreak = max($maxStreak, $running);
            $previous = $date;
        }

        return [$currentStreak, $maxStreak];
    }
}
