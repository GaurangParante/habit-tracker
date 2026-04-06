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
                            <th class="text-end">Toggle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($habits as $habit)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $habit->title }}</div>
                                    <div class="text-muted small">{{ $habit->description ?? 'No description' }}</div>
                                    <span class="badge text-bg-light text-uppercase mt-2">{{ $habit->frequency }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="habit-status-label small">Pending</span>
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

                    updateStatusLabel(event.target);
                });
            });
        </script>
    @endpush
</x-app-layout>
