<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $overall = $this->overallStats($request, $today);
        $habitStats = $this->habitStats($request, $today);

        return view('statistics', array_merge($overall, [
            'habitStats' => $habitStats,
            'today' => $today,
        ]));
    }

    public function overallStats(Request $request, Carbon $today = null): array
    {
        $today = $today ?? Carbon::today();
        $user = $request->user();
        $habitCount = $user->habits()->count();

        $weeklyStart = $today->copy()->subDays(6);
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $daysInMonth = (int) $today->daysInMonth;

        $completedToday = HabitLog::query()
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->where('habits.user_id', $user->id)
            ->whereDate('habit_logs.date', $today->toDateString())
            ->where('habit_logs.status', true)
            ->count();

        $completedWeekly = HabitLog::query()
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->where('habits.user_id', $user->id)
            ->where('habit_logs.status', true)
            ->whereBetween('habit_logs.date', [$weeklyStart->toDateString(), $today->toDateString()])
            ->count();

        $completedMonthly = HabitLog::query()
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->where('habits.user_id', $user->id)
            ->where('habit_logs.status', true)
            ->whereBetween('habit_logs.date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->count();

        $dailyPercent = $habitCount > 0
            ? (int) round(($completedToday / $habitCount) * 100)
            : 0;
        $weeklyPercent = $habitCount > 0
            ? (int) round(($completedWeekly / ($habitCount * 7)) * 100)
            : 0;
        $monthlyPercent = $habitCount > 0
            ? (int) round(($completedMonthly / ($habitCount * $daysInMonth)) * 100)
            : 0;

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

    public function habitStats(Request $request, Carbon $today = null): array
    {
        $today = $today ?? Carbon::today();
        $user = $request->user();
        $habits = $user->habits()->orderBy('created_at')->get();

        if ($habits->isEmpty()) {
            return [];
        }

        $weeklyStart = $today->copy()->subDays(6);
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $daysInMonth = (int) $today->daysInMonth;

        $weeklyCounts = HabitLog::query()
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->where('habits.user_id', $user->id)
            ->where('habit_logs.status', true)
            ->whereBetween('habit_logs.date', [$weeklyStart->toDateString(), $today->toDateString()])
            ->select('habit_logs.habit_id', DB::raw('count(*) as total'))
            ->groupBy('habit_logs.habit_id')
            ->get()
            ->keyBy('habit_id');

        $monthlyCounts = HabitLog::query()
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->where('habits.user_id', $user->id)
            ->where('habit_logs.status', true)
            ->whereBetween('habit_logs.date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->select('habit_logs.habit_id', DB::raw('count(*) as total'))
            ->groupBy('habit_logs.habit_id')
            ->get()
            ->keyBy('habit_id');

        $stats = [];
        foreach ($habits as $habit) {
            $weeklyTotal = isset($weeklyCounts[$habit->id]) ? (int) $weeklyCounts[$habit->id]->total : 0;
            $monthlyTotal = isset($monthlyCounts[$habit->id]) ? (int) $monthlyCounts[$habit->id]->total : 0;

            $stats[] = [
                'id' => $habit->id,
                'title' => $habit->title,
                'description' => $habit->description,
                'frequency' => $habit->frequency,
                'weeklyPercent' => (int) round(($weeklyTotal / 7) * 100),
                'monthlyPercent' => $daysInMonth > 0 ? (int) round(($monthlyTotal / $daysInMonth) * 100) : 0,
            ];
        }

        return $stats;
    }
}
