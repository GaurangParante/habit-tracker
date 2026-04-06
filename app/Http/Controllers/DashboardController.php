<?php

namespace App\Http\Controllers;

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
            ->addSelect('habits.*', DB::raw('COALESCE(today_logs.status, 0) as today_status'))
            ->orderBy('habits.created_at')
            ->get();

        return view('dashboard', [
            'habits' => $habits,
            'today' => $today,
        ]);
    }
}
