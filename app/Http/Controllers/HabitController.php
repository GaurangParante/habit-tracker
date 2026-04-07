<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('habits.manage');
    }

    /**
     * Display the manage habits page.
     */
    public function manage(Request $request)
    {
        $habits = $request->user()->habits()->orderBy('created_at')->get();

        return view('habits.manage', compact('habits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('habits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency_type' => ['required', 'in:daily,days_of_week,times_per_week,monthly'],
            'frequency_days' => ['nullable', 'array', 'required_if:frequency_type,days_of_week'],
            'frequency_days.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'frequency_times' => ['nullable', 'integer', 'min:1', 'max:7', 'required_if:frequency_type,times_per_week'],
            'monthly_day' => ['nullable', 'integer', 'min:1', 'max:28', 'required_if:frequency_type,monthly'],
            'target_per_day' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $payload = $this->buildFrequencyPayload($validated);
        $request->user()->habits()->create($payload);

        return redirect()->route('habits.manage')->with('status', 'Habit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $habit = $this->userHabit($id);

        return view('habits.edit', compact('habit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $habit = $this->userHabit($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency_type' => ['required', 'in:daily,days_of_week,times_per_week,monthly'],
            'frequency_days' => ['nullable', 'array', 'required_if:frequency_type,days_of_week'],
            'frequency_days.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'frequency_times' => ['nullable', 'integer', 'min:1', 'max:7', 'required_if:frequency_type,times_per_week'],
            'monthly_day' => ['nullable', 'integer', 'min:1', 'max:28', 'required_if:frequency_type,monthly'],
            'target_per_day' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $payload = $this->buildFrequencyPayload($validated);
        $habit->update($payload);

        return redirect()->route('habits.manage')->with('status', 'Habit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $habit = $this->userHabit($id);
        $habit->delete();

        return redirect()->route('habits.manage')->with('status', 'Habit deleted.');
    }

    private function userHabit(string $id): Habit
    {
        return Auth::user()->habits()->findOrFail($id);
    }

    private function buildFrequencyPayload(array $validated): array
    {
        $frequencyType = $validated['frequency_type'];
        $frequencyValue = null;
        $frequency = 'daily';

        if ($frequencyType === 'days_of_week') {
            $frequencyValue = array_values(array_unique($validated['frequency_days'] ?? []));
            $frequency = 'weekly';
        } elseif ($frequencyType === 'times_per_week') {
            $frequencyValue = ['times' => (int) ($validated['frequency_times'] ?? 1)];
            $frequency = 'weekly';
        } elseif ($frequencyType === 'monthly') {
            $frequencyValue = ['day' => (int) ($validated['monthly_day'] ?? 1)];
            $frequency = 'monthly';
        }

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'frequency' => $frequency,
            'frequency_type' => $frequencyType,
            'frequency_value' => $frequencyValue,
            'target_per_day' => (int) $validated['target_per_day'],
        ];
    }
}
