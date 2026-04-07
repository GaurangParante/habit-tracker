<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Manage Habits</h1>
                <p class="text-muted mb-0">Edit or delete habits from your list.</p>
            </div>
            <a class="btn btn-habit btn-sm" href="{{ route('habits.create') }}">
                <i class="fa-solid fa-plus me-1"></i>Add Habit
            </a>
        </div>
    </x-slot>

    <div class="habit-card rounded-4 p-4">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($habits->isEmpty())
            <div class="text-center py-5">
                <p class="text-muted mb-3">No habits yet. Add one to get started.</p>
                <a class="btn btn-habit" href="{{ route('habits.create') }}">Create Your First Habit</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Habit</th>
                            <th>Frequency</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($habits as $habit)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $habit->title }}</div>
                                    <div class="text-muted small">{{ $habit->description ?? 'No description' }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-light text-uppercase">{{ $habit->frequency_label ?? $habit->frequency }}</span>
                                    <div class="text-muted small mt-1">Target: {{ $habit->target_per_day }} / day</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('habits.edit', $habit) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('habits.destroy', $habit) }}" class="d-inline"
                                        onsubmit="return confirm('Delete this habit? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
