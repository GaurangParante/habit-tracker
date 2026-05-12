<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Habit History</h1>
                <p class="text-muted mb-0">Review past completions and missed days.</p>
            </div>
            <span class="badge text-bg-light text-uppercase">Updated {{ $today->format('M j, Y') }}</span>
        </div>
    </x-slot>

    <div class="habit-card rounded-4 p-4 mb-4">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('habits.history') }}">
            <div class="col-12 col-md-4">
                <label class="form-label">Single Date</label>
                <input class="form-control" type="date" name="date" max="{{ $today->toDateString() }}" value="{{ $selectedDate?->toDateString() }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">From</label>
                <input class="form-control" type="date" name="from" max="{{ $today->toDateString() }}" value="{{ $rangeStart?->toDateString() }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">To</label>
                <input class="form-control" type="date" name="to" max="{{ $today->toDateString() }}" value="{{ $rangeEnd?->toDateString() }}">
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button class="btn btn-habit" type="submit">
                    <i class="fa-solid fa-calendar-check me-1"></i>Apply
                </button>
            </div>
        </form>
        <p class="text-muted small mt-2 mb-0">Choose a single date or set a range to see history across multiple days.</p>
    </div>

    @if (! $isRange)
        <div class="habit-card rounded-4 p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h5 mb-1">Habits on {{ $selectedDate->format('F j, Y') }}</h2>
                    <p class="text-muted small mb-0">All habits and their status for the selected date.</p>
                </div>
                <span class="badge text-bg-light text-uppercase">{{ $selectedDate->toDateString() }}</span>
            </div>

            @if ($rows->isEmpty())
                <p class="text-muted mb-0">No habits yet. Add one to start tracking history.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Habit</th>
                                <th>Date</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $habit)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $habit->title }}</div>
                                        <div class="text-muted small">{{ $habit->description ?? 'No description' }}</div>
                                    </td>
                                    <td>{{ $habit->history_date }}</td>
                                    <td class="text-end">
                                        @if ($habit->history_status === 'completed')
                                            <span class="badge text-bg-success">
                                                <i class="fa-solid fa-check me-1"></i>Completed
                                            </span>
                                        @elseif ($habit->history_status === 'missed')
                                            <span class="badge text-bg-danger">
                                                <i class="fa-solid fa-xmark me-1"></i>Missed
                                            </span>
                                        @else
                                            <span class="badge text-bg-warning">
                                                <i class="fa-solid fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @forelse ($history as $day)
                <div class="habit-card rounded-4 p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-1">{{ $day['label'] }}</h2>
                            <p class="text-muted small mb-0">Completed {{ $day['completed'] }} of {{ $day['total'] }} habits.</p>
                        </div>
                        <span class="badge text-bg-light text-uppercase">{{ $day['date'] }}</span>
                    </div>

                    @if (empty($day['rows']))
                        <p class="text-muted mb-0">No habits yet for this date.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th>Habit</th>
                                        <th>Date</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($day['rows'] as $row)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $row['habit']->title }}</div>
                                                <div class="text-muted small">{{ $row['habit']->description ?? 'No description' }}</div>
                                            </td>
                                            <td>{{ $row['date'] }}</td>
                                            <td class="text-end">
                                                @if ($row['status'] === 'completed')
                                                    <span class="badge text-bg-success">
                                                        <i class="fa-solid fa-check me-1"></i>Completed
                                                    </span>
                                                @elseif ($row['status'] === 'missed')
                                                    <span class="badge text-bg-danger">
                                                        <i class="fa-solid fa-xmark me-1"></i>Missed
                                                    </span>
                                                @else
                                                    <span class="badge text-bg-warning">
                                                        <i class="fa-solid fa-clock me-1"></i>Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @empty
                <div class="habit-card rounded-4 p-4 text-center">
                    <p class="text-muted mb-0">No history available for this date range.</p>
                </div>
            @endforelse
        </div>
    @endif
</x-app-layout>
