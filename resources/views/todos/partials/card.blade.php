@php
    $priorityClass = match ($todo->priority) {
        'high' => 'text-bg-danger',
        'medium' => 'text-bg-warning',
        default => 'text-bg-success',
    };
    $isOverdue = $todo->due_date && $todo->due_date->lt($today) && $todo->status === 'pending';
@endphp

<div class="todo-card border rounded-4 p-3 d-flex flex-column flex-md-row justify-content-between gap-3 {{ $isOverdue ? 'border-danger' : '' }}"
    data-status="{{ $todo->status }}" data-due-date="{{ optional($todo->due_date)->format('Y-m-d') }}">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 todo-badges">
            <span class="badge {{ $priorityClass }}">{{ ucfirst($todo->priority) }}</span>
            @if ($todo->due_date)
                <span class="text-muted small">Due {{ $todo->due_date->format('M j, Y') }}</span>
            @endif
            @if ($isOverdue)
                <span class="badge text-bg-danger" data-overdue-badge="true">Overdue</span>
            @endif
        </div>
        <div class="fw-semibold">{{ $todo->title }}</div>
        <div class="text-muted small">{{ $todo->description ?? 'No description' }}</div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="form-check form-switch m-0">
            <input class="form-check-input todo-toggle" type="checkbox" role="switch"
                data-todo-id="{{ $todo->id }}" @checked($todo->status === 'completed')>
        </div>
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal"
            data-bs-target="#editTodoModal-{{ $todo->id }}">
            <i class="fa-solid fa-pen"></i>
        </button>
        <form method="POST" action="{{ route('todos.destroy', $todo) }}"
            onsubmit="return confirm('Delete this todo? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm" type="submit">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="modal fade" id="editTodoModal-{{ $todo->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('todos.update', $todo) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Todo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="title-{{ $todo->id }}">Title</label>
                        <input class="form-control" id="title-{{ $todo->id }}" name="title" value="{{ $todo->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description-{{ $todo->id }}">Description</label>
                        <textarea class="form-control" id="description-{{ $todo->id }}" name="description" rows="3">{{ $todo->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="priority-{{ $todo->id }}">Priority</label>
                        <select class="form-select" id="priority-{{ $todo->id }}" name="priority">
                            <option value="high" @selected($todo->priority === 'high')>High</option>
                            <option value="medium" @selected($todo->priority === 'medium')>Medium</option>
                            <option value="low" @selected($todo->priority === 'low')>Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="due-{{ $todo->id }}">Due date</label>
                        <input class="form-control" type="date" id="due-{{ $todo->id }}" name="due_date"
                            value="{{ optional($todo->due_date)->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-habit" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
