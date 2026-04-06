<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4">Verify Email</h1>
        <p class="text-muted">Thanks for signing up! Check your email for a verification link.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-dark" type="submit">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-dark" type="submit">Logout</button>
        </form>
    </div>
</x-guest-layout>
