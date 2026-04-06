<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'habit_id' => ['required', 'integer', 'exists:habits,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'boolean'],
        ]);

        $habit = $request->user()->habits()->findOrFail($validated['habit_id']);
        $date = Carbon::parse($validated['date'])->toDateString();

        $log = HabitLog::updateOrCreate(
            ['habit_id' => $habit->id, 'date' => $date],
            ['status' => $validated['status']]
        );

        return response()->json([
            'success' => true,
            'status' => $log->status,
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
            $data[] = isset($logs[$dateString]) && $logs[$dateString]->status ? 1 : 0;
        }

        return response()->json([
            'habit_id' => $habit->id,
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
