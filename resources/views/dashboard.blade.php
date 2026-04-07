<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Today's Habits</h1>
                <p class="text-muted mb-0">Stay focused for {{ $today->format('F j, Y') }}</p>
            </div>
            @if (session('status'))
                <span class="badge text-bg-success">{{ session('status') }}</span>
            @endif
        </div>
    </x-slot>

    <div class="habit-card rounded-4 p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Today's Habits</h2>
                <p class="text-muted small mb-0">Toggle to mark completion for today.</p>
            </div>
            <a class="btn btn-habit btn-sm" href="{{ route('habits.create') }}">
                <i class="fa-solid fa-plus me-1"></i>Add Habit
            </a>
        </div>

        @if ($habits->isEmpty())
            <div class="text-center py-5">
                <p class="text-muted mb-3">No habits yet. Add one to start tracking today.</p>
                <a class="btn btn-habit" href="{{ route('habits.create') }}">Create Your First Habit</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Habit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Streak</th>
                            <th class="text-center">Score</th>
                            <th class="text-center">Progress</th>
                            <th class="text-end">Toggle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($habits as $habit)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $habit->title }}</div>
                                    <div class="text-muted small">{{ $habit->description ?? 'No description' }}</div>
                                    <span class="badge text-bg-light text-uppercase mt-2">{{ $habit->frequency_label ?? $habit->frequency }}</span>
                                    <div class="text-muted small mt-2">Target: {{ $habit->target_per_day }} / day</div>
                                </td>
                                <td class="text-center">
                                    <span class="habit-status-label small">Pending</span>
                                    <div class="text-muted small mt-1">Missed {{ $habit->missed_this_week }}x this week</div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-semibold">{{ $habit->current_streak }}</div>
                                    <div class="text-muted small">Current</div>
                                    <div class="text-muted small">Best {{ $habit->longest_streak }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-semibold">{{ $habit->score }}%</div>
                                    <div class="text-muted small">{{ $habit->score_label }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <button class="btn btn-outline-secondary btn-sm habit-count-btn" type="button"
                                            data-action="decrease" data-habit-id="{{ $habit->id }}">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <span class="fw-semibold habit-count" data-habit-id="{{ $habit->id }}">
                                            {{ $habit->today_count }}
                                        </span>
                                        <span class="text-muted small">/ {{ $habit->target_per_day }}</span>
                                        <button class="btn btn-outline-secondary btn-sm habit-count-btn" type="button"
                                            data-action="increase" data-habit-id="{{ $habit->id }}"
                                            data-target="{{ $habit->target_per_day }}">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="habit-toggle-wrap d-inline-block">
                                        <input class="habit-toggle-input habit-toggle" type="checkbox"
                                            id="toggle-{{ $habit->id }}"
                                            data-habit-id="{{ $habit->id }}"
                                            @checked((bool) $habit->today_status)>
                                        <label class="habit-toggle-control" for="toggle-{{ $habit->id }}"
                                            aria-label="Toggle habit completion">
                                            <span class="toggle-handle"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="row g-4 mt-3">
        <div class="col-12">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Achievements</h2>
                        <p class="text-muted small mb-0">Badges unlocked as you build momentum.</p>
                    </div>
                </div>
                @if ($achievements->isEmpty())
                    <p class="text-muted mb-0">Complete habits to unlock your first badge.</p>
                @else
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($achievements as $achievement)
                            <div class="border rounded-4 px-3 py-2">
                                <div class="fw-semibold">{{ $achievement->name }}</div>
                                <div class="text-muted small">{{ $achievement->description }}</div>
                                <div class="text-muted small">Unlocked {{ optional($achievement->pivot->unlocked_at)->format('M j, Y') }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-12 col-xl-7">
            <div class="habit-card rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Today&apos;s Todos</h2>
                        <p class="text-muted small mb-0">Tasks due today.</p>
                    </div>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('todos.index') }}">
                        <i class="fa-solid fa-list-check me-1"></i>All Todos
                    </a>
                </div>

                <form id="quickTodoForm" class="d-flex flex-column flex-md-row gap-2 mb-3">
                    <input class="form-control" type="text" name="title" placeholder="Quick add a todo" required>
                    <select class="form-select" name="priority">
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <button class="btn btn-habit" type="submit">Add</button>
                </form>

                <div id="todayTodosList" class="d-flex flex-column gap-2">
                    @forelse ($todayTodos as $todo)
                        @include('todos.partials.card', ['todo' => $todo, 'today' => $today])
                    @empty
                        <p class="text-muted mb-0" data-empty>No tasks due today.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="habit-card rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Overdue</h2>
                        <p class="text-muted small mb-0">Pending tasks past due date.</p>
                    </div>
                </div>
                <div id="overdueTodosList" class="d-flex flex-column gap-2">
                    @forelse ($overdueTodos as $todo)
                        @include('todos.partials.card', ['todo' => $todo, 'today' => $today])
                    @empty
                        <p class="text-muted mb-0" data-empty>Nothing overdue.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const todayDate = "{{ $today->toDateString() }}";

            function updateStatusLabel(toggle) {
                const row = toggle.closest('tr');
                const label = row ? row.querySelector('.habit-status-label') : null;
                if (!label) {
                    return;
                }
                if (toggle.checked) {
                    label.textContent = 'Completed';
                    label.classList.add('is-complete');
                } else {
                    label.textContent = 'Pending';
                    label.classList.remove('is-complete');
                }
            }

            document.querySelectorAll('.habit-toggle').forEach((toggle) => {
                updateStatusLabel(toggle);
                toggle.addEventListener('change', async (event) => {
                    const habitId = event.target.dataset.habitId;
                    const status = event.target.checked ? 1 : 0;

                    const response = await fetch("{{ route('habits.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ habit_id: habitId, date: todayDate, status }),
                    });

                    if (!response.ok) {
                        event.target.checked = !event.target.checked;
                        updateStatusLabel(event.target);
                        alert('Unable to update habit. Please try again.');
                        return;
                    }

                    const data = await response.json();
                    const countDisplay = document.querySelector(`.habit-count[data-habit-id="${habitId}"]`);
                    if (countDisplay && typeof data.count !== 'undefined') {
                        countDisplay.textContent = data.count;
                    }
                    updateStatusLabel(event.target);
                });
            });

            function syncPlusButtons() {
                document.querySelectorAll('.habit-count-btn[data-action="increase"]').forEach((btn) => {
                    const habitId = btn.dataset.habitId;
                    const target = parseInt(btn.dataset.target, 10) || 1;
                    const countDisplay = document.querySelector(`.habit-count[data-habit-id="${habitId}"]`);
                    const count = countDisplay ? (parseInt(countDisplay.textContent, 10) || 0) : 0;
                    btn.disabled = count >= target;
                });
            }

            syncPlusButtons();

            document.querySelectorAll('.habit-count-btn').forEach((btn) => {
                btn.addEventListener('click', async (event) => {
                    const habitId = event.currentTarget.dataset.habitId;
                    const action = event.currentTarget.dataset.action;
                    const countDisplay = document.querySelector(`.habit-count[data-habit-id="${habitId}"]`);
                    const toggle = document.getElementById(`toggle-${habitId}`);
                    if (!countDisplay || !toggle) return;

                    let count = parseInt(countDisplay.textContent, 10) || 0;
                    if (action === 'increase') {
                        const target = parseInt(event.currentTarget.dataset.target, 10) || 1;
                        if (count >= target) {
                            syncPlusButtons();
                            return;
                        }
                        count = count + 1;
                    } else {
                        count = Math.max(0, count - 1);
                    }

                    const response = await fetch("{{ route('habits.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ habit_id: habitId, date: todayDate, status: count > 0 ? 1 : 0, count }),
                    });

                    if (!response.ok) {
                        alert('Unable to update habit. Please try again.');
                        return;
                    }

                    const data = await response.json();
                    countDisplay.textContent = data.count ?? count;
                    toggle.checked = data.status ?? count > 0;
                    updateStatusLabel(toggle);
                    syncPlusButtons();
                });
            });

            const quickTodoForm = document.getElementById('quickTodoForm');
            if (quickTodoForm) {
                quickTodoForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const formData = new FormData(quickTodoForm);
                    const payload = {
                        title: formData.get('title'),
                        priority: formData.get('priority'),
                        due_date: "{{ $today->toDateString() }}",
                    };

                    const response = await fetch("{{ route('todos.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        alert('Unable to add todo.');
                        return;
                    }

                    window.location.reload();
                });
            }

            function syncDashboardTodoEmptyStates() {
                const groups = [
                    { id: 'todayTodosList', emptyText: 'No tasks due today.' },
                    { id: 'overdueTodosList', emptyText: 'Nothing overdue.' },
                ];

                groups.forEach((group) => {
                    const wrap = document.getElementById(group.id);
                    if (!wrap) return;
                    const hasCards = !!wrap.querySelector('.todo-card');
                    const empty = wrap.querySelector('[data-empty]');
                    if (!hasCards && !empty) {
                        const message = document.createElement('p');
                        message.className = 'text-muted mb-0';
                        message.dataset.empty = 'true';
                        message.textContent = group.emptyText;
                        wrap.appendChild(message);
                    }
                    if (hasCards && empty) {
                        empty.remove();
                    }
                });
            }

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
                    const todayWrap = document.getElementById('todayTodosList');
                    const overdueWrap = document.getElementById('overdueTodosList');
                    if (!card || !todayWrap || !overdueWrap) return;

                    card.dataset.status = data.status;
                    const dueDate = card.dataset.dueDate;
                    const isOverdue = dueDate && dueDate < todayDate;
                    const badge = card.querySelector('[data-overdue-badge]');
                    const badgeRow = card.querySelector('.todo-badges');

                    if (data.status === 'completed') {
                        if (badge) badge.remove();
                        card.classList.remove('border-danger');
                        card.remove();
                    } else {
                        if (isOverdue && !badge) {
                            const badgeEl = document.createElement('span');
                            badgeEl.className = 'badge text-bg-danger';
                            badgeEl.dataset.overdueBadge = 'true';
                            badgeEl.textContent = 'Overdue';
                            if (badgeRow) badgeRow.appendChild(badgeEl);
                        }
                        if (!isOverdue && badge) badge.remove();
                        card.classList.toggle('border-danger', !!isOverdue);

                        if (isOverdue) {
                            overdueWrap.prepend(card);
                        } else if (dueDate === todayDate) {
                            todayWrap.prepend(card);
                        }
                    }

                    syncDashboardTodoEmptyStates();
                });
            });
        </script>
    @endpush
</x-app-layout>
