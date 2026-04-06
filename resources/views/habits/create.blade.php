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
                        <label class="form-label" for="frequency">Frequency</label>
                        <select class="form-select" id="frequency" name="frequency" required>
                            <option value="">Select frequency</option>
                            <option value="daily" @selected(old('frequency') === 'daily')>Daily</option>
                            <option value="weekly" @selected(old('frequency') === 'weekly')>Weekly</option>
                            <option value="monthly" @selected(old('frequency') === 'monthly')>Monthly</option>
                        </select>
                        @error('frequency')
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
</x-app-layout>
