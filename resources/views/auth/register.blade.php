<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4">Create an Account</h1>
        <p class="text-muted">Start building better routines.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-dark" type="submit">Register</button>
        </div>
    </form>

    <div class="text-center mt-3">
        <span class="text-muted">Already have an account?</span>
        <a href="{{ route('login') }}">Login</a>
    </div>
</x-guest-layout>
