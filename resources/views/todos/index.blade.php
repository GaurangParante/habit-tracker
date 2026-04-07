<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Todos</h1>
                <p class="text-muted mb-0">Stay on top of your tasks alongside your habits.</p>
            </div>
            @if (session('status'))
                <span class="badge text-bg-success">{{ session('status') }}</span>
            @endif
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="habit-card rounded-4 p-4">
                <h2 class="h5 mb-3">Add Todo</h2>
                <form method="POST" action="{{ route('todos.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="priority">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="high" @selected(old('priority') === 'high')>High</option>
                            <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                            <option value="low" @selected(old('priority') === 'low')>Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="due_date">Due date</label>
                        <input class="form-control" type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
                    </div>
                    <button class="btn btn-habit" type="submit">Add Todo</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Pending</h2>
                    <span class="text-muted small">{{ $pendingTodos->count() }} tasks</span>
                </div>
                <div id="pendingTodos" class="d-flex flex-column gap-3">
                    @forelse ($pendingTodos as $todo)
                        @include('todos.partials.card', ['todo' => $todo, 'today' => $today])
                    @empty
                        <p class="text-muted mb-0" data-empty>No pending tasks. Great job!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Completed</h2>
                    <span class="text-muted small">{{ $completedTodos->count() }} tasks</span>
                </div>
                <div id="completedTodos" class="d-flex flex-column gap-3">
                    @forelse ($completedTodos as $todo)
                        @include('todos.partials.card', ['todo' => $todo, 'today' => $today])
                    @empty
                        <p class="text-muted mb-0" data-empty>Completed tasks will show up here.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('.todo-toggle').forEach((toggle) => {
                toggle.addEventListener('change', async (event) => {
                    const todoId = event.target.dataset.todoId;
                    const response = await fetch("{{ route('todos.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ todo_id: todoId }),
                    });

                    if (!response.ok) {
                        event.target.checked = !event.target.checked;
                        alert('Unable to update todo.');
                        return;
                    }

                    const data = await response.json();
                    const card = event.target.closest('.todo-card');
                    const pendingWrap = document.getElementById('pendingTodos');
                    const completedWrap = document.getElementById('completedTodos');
                    if (!card || !pendingWrap || !completedWrap) return;

                    card.classList.remove('opacity-50');
                    card.dataset.status = data.status;

                    const dueDate = card.dataset.dueDate;
                    const isOverdue = dueDate && dueDate < "{{ $today->toDateString() }}";
                    const badge = card.querySelector('[data-overdue-badge]');
                    if (data.status === 'completed') {
                        if (badge) badge.remove();
                        card.classList.remove('border-danger');
                        completedWrap.prepend(card);
                    } else {
                        if (isOverdue && !badge) {
                            const badgeEl = document.createElement('span');
                            badgeEl.className = 'badge text-bg-danger';
                            badgeEl.dataset.overdueBadge = 'true';
                            badgeEl.textContent = 'Overdue';
                            const badgeRow = card.querySelector('.todo-badges');
                            if (badgeRow) badgeRow.appendChild(badgeEl);
                        }
                        if (!isOverdue && badge) badge.remove();
                        card.classList.toggle('border-danger', !!isOverdue);
                        pendingWrap.prepend(card);
                    }

                    [pendingWrap, completedWrap].forEach((wrap) => {
                        const hasCards = !!wrap.querySelector('.todo-card');
                        const empty = wrap.querySelector('[data-empty]');
                        if (!hasCards && !empty) {
                            const message = document.createElement('p');
                            message.className = 'text-muted mb-0';
                            message.dataset.empty = 'true';
                            message.textContent = wrap.id === 'pendingTodos'
                                ? 'No pending tasks. Great job!'
                                : 'Completed tasks will show up here.';
                            wrap.appendChild(message);
                        }
                        if (hasCards && empty) {
                            empty.remove();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
