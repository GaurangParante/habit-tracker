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
            'frequency' => ['required', 'in:daily,weekly,monthly'],
        ]);

        $request->user()->habits()->create($validated);

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
            'frequency' => ['required', 'in:daily,weekly,monthly'],
        ]);

        $habit->update($validated);

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
}
