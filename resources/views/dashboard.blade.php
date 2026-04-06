<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Your Habit Dashboard</h1>
                <p class="text-muted mb-0">Track progress for {{ $today->format('F j, Y') }}</p>
            </div>
            @if (session('status'))
                <span class="badge text-bg-success">{{ session('status') }}</span>
            @endif
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="habit-card rounded-4 p-4 shadow-sm">
                <h2 class="h5 mb-3">Add Habit</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('habits.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="frequency">Frequency</label>
                        <select class="form-select" id="frequency" name="frequency" required>
                            <option value="">Select frequency</option>
                            <option value="daily" @selected(old('frequency') === 'daily')>Daily</option>
                            <option value="weekly" @selected(old('frequency') === 'weekly')>Weekly</option>
                            <option value="monthly" @selected(old('frequency') === 'monthly')>Monthly</option>
                        </select>
                    </div>
                    <button class="btn btn-dark w-100" type="submit">Create Habit</button>
                </form>
            </div>

            <div class="habit-card rounded-4 p-4 shadow-sm mt-4">
                <h2 class="h5 mb-3">Your Habits</h2>
                @forelse ($habits as $habit)
                    <div class="border rounded-3 p-3 mb-3 {{ $habitStats[$habit->id]['today_completed'] ? 'border-success habit-gradient' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="h6 mb-1">{{ $habit->title }}</h3>
                                <p class="text-muted small mb-2">{{ $habit->description ?? 'No description' }}</p>
                                <span class="badge text-bg-light text-uppercase">{{ $habit->frequency }}</span>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('habits.edit', $habit) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('habits.destroy', $habit) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Current Streak: {{ $habitStats[$habit->id]['current_streak'] }} days</span>
                                <span>Max: {{ $habitStats[$habit->id]['max_streak'] }} days</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $habitStats[$habit->id]['progress_percent'] }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-2">
                                <span>Completed days: {{ $habitStats[$habit->id]['completed_total'] }}</span>
                                <span>{{ $habitStats[$habit->id]['progress_percent'] }}% (last 30 days)</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No habits yet. Add one to get started.</p>
                @endforelse
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="habit-card rounded-4 p-4 shadow-sm">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Today's Tracking</h2>
                    <span class="text-muted small">Toggle to update status instantly.</span>
                </div>

                @if ($habits->isEmpty())
                    <p class="text-muted">Create a habit to start tracking.</p>
                @else
                    <div class="list-group">
                        @foreach ($habits as $habit)
                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $habit->title }}</div>
                                    <small class="text-muted text-uppercase">{{ $habit->frequency }}</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input habit-toggle" type="checkbox" role="switch"
                                        data-habit-id="{{ $habit->id }}"
                                        @checked((bool) $habit->today_status)>
                                    <span class="habit-status-label ms-3">Pending</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="row g-4 mt-1">
                <div class="col-12">
                    <div class="habit-card rounded-4 p-4 shadow-sm">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Progress Charts</h2>
                            <select id="chartHabitSelect" class="form-select w-auto">
                                @foreach ($habits as $habit)
                                    <option value="{{ $habit->id }}">{{ $habit->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="p-3 border rounded-3">
                                    <h3 class="h6">Weekly (Last 7 Days)</h3>
                                    <canvas id="weeklyChart" height="180"></canvas>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="p-3 border rounded-3">
                                    <h3 class="h6">Monthly (Last 30 Days)</h3>
                                    <canvas id="monthlyChart" height="180"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .habit-status-label {
                min-width: 88px;
                text-align: right;
                font-size: 0.85rem;
                transition: color 0.2s ease, transform 0.2s ease;
            }

            .habit-status-label.is-complete {
                color: var(--habit-accent);
                font-weight: 600;
                transform: translateY(-1px);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const todayDate = "{{ $today->toDateString() }}";

            function updateStatusLabel(toggle) {
                const label = toggle.closest('.form-switch').querySelector('.habit-status-label');
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

                    updateStatusLabel(event.target);
                });
            });

            const weeklyCtx = document.getElementById('weeklyChart');
            const monthlyCtx = document.getElementById('monthlyChart');
            const habitSelect = document.getElementById('chartHabitSelect');
            let weeklyChart = null;
            let monthlyChart = null;
            const weeklyUrlTemplate = "{{ route('habits.weekly', ['id' => '__HABIT__']) }}";
            const monthlyUrlTemplate = "{{ route('habits.monthly', ['id' => '__HABIT__']) }}";

            function buildHabitUrl(template, habitId) {
                return template.replace('__HABIT__', habitId);
            }

            async function loadCharts(habitId) {
                const weeklyResponse = await fetch(buildHabitUrl(weeklyUrlTemplate, habitId));
                const weeklyData = await weeklyResponse.json();

                const monthlyResponse = await fetch(buildHabitUrl(monthlyUrlTemplate, habitId));
                const monthlyData = await monthlyResponse.json();

                if (weeklyChart) {
                    weeklyChart.destroy();
                }
                if (monthlyChart) {
                    monthlyChart.destroy();
                }

                weeklyChart = new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyData.labels,
                        datasets: [{
                            label: 'Completed',
                            data: weeklyData.data,
                            backgroundColor: 'rgba(42, 124, 95, 0.6)',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });

                monthlyChart = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [{
                            label: 'Completed',
                            data: monthlyData.data,
                            borderColor: 'rgba(42, 124, 95, 0.9)',
                            backgroundColor: 'rgba(42, 124, 95, 0.2)',
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }

            if (habitSelect && habitSelect.value) {
                loadCharts(habitSelect.value);
                habitSelect.addEventListener('change', (event) => {
                    loadCharts(event.target.value);
                });
            }
        </script>
    @endpush
</x-app-layout>
