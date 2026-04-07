<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use App\Services\HabitAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(Request $request, HabitAnalyticsService $analytics)
    {
        $today = Carbon::today();
        $overall = $this->overallStats($request, $today, $analytics);
        $habitStats = $this->habitStats($request, $today, $analytics);
        $weeklyPattern = $analytics->getWeeklyPattern($request->user(), $today);
        $bestWorst = $analytics->getBestWorstHabits($request->user(), $today);
        $heatmap = $analytics->getHeatmapData($request->user(), $today);

        return view('statistics', array_merge($overall, [
            'habitStats' => $habitStats,
            'weeklyPattern' => $weeklyPattern,
            'bestHabit' => $bestWorst['best'] ?? null,
            'worstHabit' => $bestWorst['worst'] ?? null,
            'heatmap' => $heatmap,
            'today' => $today,
        ]));
    }

    public function overallStats(Request $request, Carbon $today = null, ?HabitAnalyticsService $analytics = null): array
    {
        $today = $today ?? Carbon::today();
        $analytics = $analytics ?? app(HabitAnalyticsService::class);
        $user = $request->user();
        $habits = $user->habits()->get();
        $habitCount = $habits->count();

        $weeklyStart = $today->copy()->subDays(6);
        $monthStart = $today->copy()->startOfMonth();
        $daysInMonth = (int) $today->daysInMonth;
        $rangeStart = $monthStart->lt($weeklyStart) ? $monthStart : $weeklyStart;

        $habitIds = $habits->pluck('id');
        $logsByHabit = HabitLog::query()
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('date', [$rangeStart->toDateString(), $today->toDateString()])
            ->get()
            ->groupBy('habit_id');

        $completedToday = 0;
        $completedWeekly = 0;
        $completedMonthly = 0;
        $possibleToday = 0;
        $possibleWeekly = 0;
        $possibleMonthly = 0;

        foreach ($habits as $habit) {
            $logs = $logsByHabit->get($habit->id, collect());
            $todayLogs = $logs->filter(fn ($log) => $log->date->toDateString() === $today->toDateString());

            $scoreWeekly = $analytics->calculateScore($habit, $logs, $today, 7);
            $scoreMonthly = $analytics->calculateScore($habit, $logs, $today, $daysInMonth);

            $completedWeekly += $scoreWeekly['completed'];
            $possibleWeekly += $scoreWeekly['possible'];
            $completedMonthly += $scoreMonthly['completed'];
            $possibleMonthly += $scoreMonthly['possible'];

            $todayScore = $analytics->calculateScore($habit, $todayLogs, $today, 1);
            if ($todayScore['possible'] > 0) {
                $possibleToday++;
                if ($todayScore['completed'] > 0) {
                    $completedToday++;
                }
            }
        }

        $dailyPercent = $possibleToday > 0 ? (int) round(($completedToday / $possibleToday) * 100) : 0;
        $weeklyPercent = $possibleWeekly > 0 ? (int) round(($completedWeekly / $possibleWeekly) * 100) : 0;
        $monthlyPercent = $possibleMonthly > 0 ? (int) round(($completedMonthly / $possibleMonthly) * 100) : 0;

        return [
            'dailyPercent' => $dailyPercent,
            'weeklyPercent' => $weeklyPercent,
            'monthlyPercent' => $monthlyPercent,
            'completedToday' => $completedToday,
            'completedWeekly' => $completedWeekly,
            'completedMonthly' => $completedMonthly,
            'habitCount' => $habitCount,
            'daysInMonth' => $daysInMonth,
        ];
    }

    public function habitStats(Request $request, Carbon $today = null, ?HabitAnalyticsService $analytics = null): array
    {
        $today = $today ?? Carbon::today();
        $user = $request->user();
        $habits = $user->habits()->orderBy('created_at')->get();

        if ($habits->isEmpty()) {
            return [];
        }

        $analytics = $analytics ?? app(HabitAnalyticsService::class);
        $monthStart = $today->copy()->startOfMonth();
        $habitIds = $habits->pluck('id');
        $logsByHabit = HabitLog::query()
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('date', [$monthStart->toDateString(), $today->toDateString()])
            ->get()
            ->groupBy('habit_id');

        $stats = [];
        foreach ($habits as $habit) {
            $logs = $logsByHabit->get($habit->id, collect());
            $weeklyScore = $analytics->calculateScore($habit, $logs, $today, 7);
            $monthlyScore = $analytics->calculateScore($habit, $logs, $today, (int) $today->daysInMonth);

            $stats[] = [
                'id' => $habit->id,
                'title' => $habit->title,
                'description' => $habit->description,
                'frequency' => $habit->frequency_label ?? $habit->frequency,
                'weeklyPercent' => $weeklyScore['score'],
                'monthlyPercent' => $monthlyScore['score'],
            ];
        }

        return $stats;
    }
}
