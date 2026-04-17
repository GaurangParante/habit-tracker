<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use App\Services\HabitAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request, HabitAnalyticsService $analytics)
    {
        $today = Carbon::today();
        $todayString = $today->toDateString();
        $user = $request->user();

        $habits = $user->habits()
            ->leftJoin('habit_logs as today_logs', function ($join) use ($todayString) {
                $join->on('habits.id', '=', 'today_logs.habit_id')
                    ->where('today_logs.date', '=', $todayString);
            })
            ->addSelect(
                'habits.*',
                DB::raw('COALESCE(today_logs.count, 0) as today_count'),
                DB::raw("CASE
                    WHEN COALESCE(today_logs.count, 0) >= habits.target_per_day OR today_logs.status = 'completed' THEN 'completed'
                    WHEN today_logs.status = 'missed' THEN 'missed'
                    ELSE 'pending'
                END as today_status")
            )
            ->orderBy('habits.created_at')
            ->get();

        $todayTodos = $user->todos()
            ->whereDate('due_date', $todayString)
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->get();

        $overdueTodos = $user->todos()
            ->where('status', 'pending')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayString)
            ->orderBy('due_date')
            ->get();

        $habitIds = $habits->pluck('id');
        $logsByHabit = HabitLog::query()
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('date', [$today->copy()->subDays(365)->toDateString(), $todayString])
            ->get()
            ->groupBy('habit_id');

        foreach ($habits as $habit) {
            $logs = $logsByHabit->get($habit->id, collect());
            $streaks = $analytics->calculateStreak($habit, $logs, $today);
            $score = $analytics->calculateScore($habit, $logs, $today);
            $missed = $analytics->getMissedDays($habit, $logs, $today);

            $habit->setAttribute('current_streak', $streaks['current']);
            $habit->setAttribute('longest_streak', $streaks['longest']);
            $habit->setAttribute('score', $score['score']);
            $habit->setAttribute('score_label', $score['label']);
            $habit->setAttribute('missed_this_week', $missed);
        }

        return view('dashboard', [
            'habits' => $habits,
            'today' => $today,
            'achievements' => $user->achievements()->orderByPivot('unlocked_at', 'desc')->get(),
            'todayTodos' => $todayTodos,
            'overdueTodos' => $overdueTodos,
        ]);
    }
}
