<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use App\Services\HabitAnalyticsService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function toggle(Request $request, HabitAnalyticsService $analytics)
    {
        $validated = $request->validate([
            'habit_id' => ['required', 'integer', 'exists:habits,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'boolean'],
            'count' => ['nullable', 'integer', 'min:0'],
        ]);

        $habit = $request->user()->habits()->findOrFail($validated['habit_id']);
        $date = Carbon::parse($validated['date'])->toDateString();
        $target = max(1, (int) $habit->target_per_day);
        $count = isset($validated['count'])
            ? (int) $validated['count']
            : ((bool) $validated['status'] ? $target : 0);
        $status = $count >= $target;

        $log = HabitLog::updateOrCreate(
            ['habit_id' => $habit->id, 'date' => $date],
            ['status' => $status, 'count' => $count]
        );

        $analytics->updateHabitStreaks($habit);
        $analytics->syncAchievements($request->user());

        return response()->json([
            'success' => true,
            'status' => $log->status,
            'count' => $log->count,
            'date' => $log->date->toDateString(),
        ]);
    }

    public function weekly(string $id, Request $request)
    {
        return $this->stats($id, 7, $request);
    }

    public function monthly(string $id, Request $request)
    {
        return $this->stats($id, 30, $request);
    }

    private function stats(string $id, int $days, Request $request)
    {
        $habit = $request->user()->habits()->findOrFail($id);
        $today = Carbon::today();
        $start = $today->copy()->subDays($days - 1);

        $logs = $habit->logs()
            ->whereBetween('date', [$start->toDateString(), $today->toDateString()])
            ->get()
            ->keyBy(fn ($log) => $log->date->toDateString());

        $period = CarbonPeriod::create($start, $today);

        $labels = [];
        $data = [];

        foreach ($period as $date) {
            $dateString = $date->toDateString();
            $labels[] = $date->format('M d');
            $data[] = isset($logs[$dateString]) && ($logs[$dateString]->count >= max(1, (int) $habit->target_per_day)) ? 1 : 0;
        }

        return response()->json([
            'habit_id' => $habit->id,
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
