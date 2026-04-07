<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-1">Add Habit</h1>
            <p class="text-muted mb-0">Define a new habit and its tracking frequency.</p>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="habit-card rounded-4 p-4">
                <form method="POST" action="{{ route('habits.store') }}">
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
                        <label class="form-label" for="frequency_type">Frequency</label>
                        <select class="form-select" id="frequency_type" name="frequency_type" required>
                            <option value="">Select frequency</option>
                            <option value="daily" @selected(old('frequency_type') === 'daily')>Daily</option>
                            <option value="days_of_week" @selected(old('frequency_type') === 'days_of_week')>Specific days</option>
                            <option value="times_per_week" @selected(old('frequency_type') === 'times_per_week')>X times per week</option>
                            <option value="monthly" @selected(old('frequency_type') === 'monthly')>Monthly</option>
                        </select>
                        @error('frequency_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" data-frequency="days_of_week">
                        <label class="form-label">Select days</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach (['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $value => $label)
                                <label class="btn btn-outline-secondary btn-sm">
                                    <input class="form-check-input me-1" type="checkbox" name="frequency_days[]"
                                        value="{{ $value }}" @checked(in_array($value, old('frequency_days', []), true))>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        @error('frequency_days')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" data-frequency="times_per_week">
                        <label class="form-label" for="frequency_times">Times per week</label>
                        <input class="form-control" type="number" min="1" max="7" id="frequency_times" name="frequency_times"
                            value="{{ old('frequency_times', 3) }}">
                        @error('frequency_times')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" data-frequency="monthly">
                        <label class="form-label" for="monthly_day">Day of month</label>
                        <input class="form-control" type="number" min="1" max="28" id="monthly_day" name="monthly_day"
                            value="{{ old('monthly_day', 1) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="target_per_day">Target per day</label>
                        <input class="form-control" type="number" min="1" max="50" id="target_per_day" name="target_per_day"
                            value="{{ old('target_per_day', 1) }}" required>
                        @error('target_per_day')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-habit" type="submit">Save Habit</button>
                        <a class="btn btn-outline-secondary" href="{{ route('habits.manage') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const frequencySelect = document.getElementById('frequency_type');
            const sections = document.querySelectorAll('[data-frequency]');

            function syncFrequencySections() {
                const value = frequencySelect ? frequencySelect.value : '';
                sections.forEach((section) => {
                    section.style.display = section.dataset.frequency === value ? 'block' : 'none';
                });
            }

            if (frequencySelect) {
                frequencySelect.addEventListener('change', syncFrequencySections);
                syncFrequencySections();
            }
        </script>
    @endpush
</x-app-layout>
