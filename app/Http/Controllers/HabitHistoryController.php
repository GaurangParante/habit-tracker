<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
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
            ->map(function (Habit $habit) use ($selectedDate) {
                $completed = $this->joinedLogIsCompleted($habit);
                $habit->history_date = $selectedDate->toDateString();
                $habit->history_status = $completed;
                $habit->history_label = $completed ? 'Completed' : 'Missed';
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
                $completed = $this->logIsCompleted($habit, $log);
                if ($completed) {
                    $completedCount++;
                }
                $rows[] = [
                    'habit' => $habit,
                    'date' => $dateKey,
                    'completed' => $completed,
                    'label' => $completed ? 'Completed' : 'Missed',
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

    private function joinedLogIsCompleted(Habit $habit): bool
    {
        if (! $habit->log_date) {
            return false;
        }

        $target = max(1, (int) $habit->target_per_day);
        $count = (int) ($habit->log_count ?? 0);
        $status = (bool) ($habit->log_status ?? false);

        return $count >= $target || $status;
    }

    private function logIsCompleted(Habit $habit, ?HabitLog $log): bool
    {
        if (! $log) {
            return false;
        }

        $target = max(1, (int) $habit->target_per_day);
        $count = (int) ($log->count ?? 0);
        $status = (bool) ($log->status ?? false);

        return $count >= $target || $status;
    }
}
