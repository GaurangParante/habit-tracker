<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $todos = $request->user()->todos()
            ->orderByRaw("FIELD(status, 'pending', 'completed')")
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get();

        $pending = $todos->where('status', 'pending');
        $completed = $todos->where('status', 'completed');

        return view('todos.index', [
            'pendingTodos' => $pending,
            'completedTodos' => $completed,
            'today' => Carbon::today(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        $request->user()->todos()->create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('todos.index')->with('status', 'Todo created.');
    }

    public function update(Request $request, string $id)
    {
        $todo = $this->userTodo($request, $id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,completed'],
        ]);

        $todo->update($validated);

        return redirect()->route('todos.index')->with('status', 'Todo updated.');
    }

    public function destroy(Request $request, string $id)
    {
        $todo = $this->userTodo($request, $id);
        $todo->delete();

        return redirect()->route('todos.index')->with('status', 'Todo deleted.');
    }

    public function toggleStatus(Request $request)
    {
        $validated = $request->validate([
            'todo_id' => ['required', 'integer', 'exists:todos,id'],
        ]);

        $todo = $this->userTodo($request, $validated['todo_id']);
        $todo->status = $todo->status === 'completed' ? 'pending' : 'completed';
        $todo->save();

        return response()->json([
            'success' => true,
            'status' => $todo->status,
            'todo' => [
                'id' => $todo->id,
                'title' => $todo->title,
                'description' => $todo->description,
                'priority' => $todo->priority,
                'due_date' => optional($todo->due_date)->format('Y-m-d'),
            ],
        ]);
    }

    private function userTodo(Request $request, string $id): Todo
    {
        return $request->user()->todos()->findOrFail($id);
    }
}
