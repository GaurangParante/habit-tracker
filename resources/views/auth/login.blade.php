<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4">Welcome Back</h1>
        <p class="text-muted">Sign in to continue tracking your habits.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
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

        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-dark" type="submit">Login</button>
            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">Forgot your password?</a>
            @endif
        </div>
    </form>

    <div class="text-center mt-3">
        <span class="text-muted">Don't have an account?</span>
        <a href="{{ route('register') }}">Register</a>
    </div>
</x-guest-layout>
