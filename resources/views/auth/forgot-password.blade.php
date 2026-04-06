<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4">Reset Password</h1>
        <p class="text-muted">We will email you a reset link.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-dark" type="submit">Send Reset Link</button>
        </div>
    </form>
</x-guest-layout>
