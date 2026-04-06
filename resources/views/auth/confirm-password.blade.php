<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4">Confirm Password</h1>
        <p class="text-muted">Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-dark" type="submit">Confirm</button>
        </div>
    </form>
</x-guest-layout>
