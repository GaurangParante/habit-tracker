<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Services\HabitAnalyticsService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class HabitHistoryController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $today = Carbon::today();
        $selectedDate = isset($validated['date']) ? Carbon::parse($validated['date']) : null;
        $rangeStart = isset($validated['from']) ? Carbon::parse($validated['from']) : null;
        $rangeEnd = isset($validated['to']) ? Carbon::parse($validated['to']) : null;

        if ($rangeStart && ! $rangeEnd) {
            $rangeEnd = $rangeStart->copy();
        }

        if ($rangeEnd && ! $rangeStart) {
            $rangeStart = $rangeEnd->copy();
        }

        $isRange = (bool) ($rangeStart && $rangeEnd);
        if (! $isRange) {
            $selectedDate = $selectedDate ?? $today->copy();
            if ($selectedDate->gt($today)) {
                $selectedDate = $today->copy();
            }
        } else {
            if ($rangeStart && $rangeStart->gt($today)) {
                $rangeStart = $today->copy();
            }
            if ($rangeEnd && $rangeEnd->gt($today)) {
                $rangeEnd = $today->copy();
            }
        }

        if ($isRange && $rangeStart->gt($rangeEnd)) {
            [$rangeStart, $rangeEnd] = [$rangeEnd, $rangeStart];
        }

        if ($isRange) {
            return $this->rangeView($request, $rangeStart, $rangeEnd, $today);
        }

        return $this->singleDateView($request, $selectedDate, $today);
    }

    private function singleDateView(Request $request, Carbon $selectedDate, Carbon $today)
    {
        $analytics = app(HabitAnalyticsService::class);
        $rows = Habit::query()
            ->where('habits.user_id', $request->user()->id)
            ->leftJoin('habit_logs', function ($join) use ($selectedDate) {
                $join->on('habits.id', '=', 'habit_logs.habit_id')
                    ->whereDate('habit_logs.date', $selectedDate->toDateString());
            })
            ->select([
                'habits.*',
                'habit_logs.status as log_status',
                'habit_logs.count as log_count',
                'habit_logs.date as log_date',
            ])
            ->orderBy('habits.created_at')
            ->get()
            ->map(function (Habit $habit) use ($selectedDate, $analytics) {
                $status = $this->joinedLogStatus($habit, $selectedDate, $analytics);
                $habit->history_date = $selectedDate->toDateString();
                $habit->history_status = $status;
                $habit->history_label = ucfirst($status);

                return $habit;
            });

        return view('habits.history', [
            'today' => $today,
            'isRange' => false,
            'selectedDate' => $selectedDate,
            'rangeStart' => null,
            'rangeEnd' => null,
            'rows' => $rows,
            'history' => [],
        ]);
    }

    private function rangeView(Request $request, Carbon $rangeStart, Carbon $rangeEnd, Carbon $today)
    {
        $analytics = app(HabitAnalyticsService::class);
        $habits = $request->user()->habits()->orderBy('created_at')->get();

        $logs = HabitLog::query()
            ->whereIn('habit_id', $habits->pluck('id'))
            ->whereBetween('date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->get()
            ->groupBy(fn (HabitLog $log) => $log->date->toDateString())
            ->map(fn ($group) => $group->keyBy('habit_id'));

        $history = [];
        foreach (CarbonPeriod::create($rangeStart, $rangeEnd) as $date) {
            $dateKey = $date->toDateString();
            $dayLogs = $logs->get($dateKey, collect());
            $rows = [];
            $completedCount = 0;

            foreach ($habits as $habit) {
                $log = $dayLogs->get($habit->id);
                $status = $analytics->resolveStatusForDate($habit, $log, $date);
                if ($status === 'completed') {
                    $completedCount++;
                }
                $rows[] = [
                    'habit' => $habit,
                    'date' => $dateKey,
                    'status' => $status,
                    'label' => ucfirst($status),
                ];
            }

            $history[] = [
                'date' => $dateKey,
                'label' => $date->format('M j, Y'),
                'completed' => $completedCount,
                'total' => $habits->count(),
                'rows' => $rows,
            ];
        }

        return view('habits.history', [
            'today' => $today,
            'isRange' => true,
            'selectedDate' => $rangeStart->copy(),
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'rows' => collect(),
            'history' => $history,
        ]);
    }

    private function joinedLogStatus(Habit $habit, Carbon $date, HabitAnalyticsService $analytics): string
    {
        if (! $habit->log_date) {
            return $analytics->isDateEligibleForStatus($habit, $date) && $date->isPast() ? 'missed' : 'pending';
        }

        $log = new HabitLog([
            'date' => $habit->log_date,
            'count' => (int) ($habit->log_count ?? 0),
            'status' => $habit->log_status,
        ]);

        return $analytics->resolveStatusForDate($habit, $log, $date);
    }
}
