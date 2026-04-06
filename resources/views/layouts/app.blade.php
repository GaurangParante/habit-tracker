<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            :root {
                --habit-accent: #2a7c5f;
                --habit-card: #ffffff;
                --habit-bg: #f4f7f6;
            }

            [data-bs-theme="dark"] {
                --habit-card: #1f2427;
                --habit-bg: #121416;
            }

            body {
                font-family: "Figtree", sans-serif;
                background: var(--habit-bg);
            }

            .habit-card {
                background: var(--habit-card);
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .habit-accent {
                color: var(--habit-accent);
            }

            .habit-gradient {
                background: linear-gradient(120deg, rgba(42, 124, 95, 0.1), rgba(26, 78, 61, 0.05));
            }

            .progress-bar {
                background-color: var(--habit-accent);
            }
        </style>
        @stack('styles')
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand fw-semibold habit-accent" href="{{ route('dashboard') }}">
                    Habit Tracker
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <button id="themeToggle" class="btn btn-outline-secondary btn-sm" type="button">
                            Toggle Theme
                        </button>
                        @auth
                            <span class="small text-muted">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button class="btn btn-outline-dark btn-sm" type="submit">Logout</button>
                            </form>
                        @else
                            <a class="btn btn-outline-dark btn-sm" href="{{ route('login') }}">Login</a>
                            <a class="btn btn-dark btn-sm" href="{{ route('register') }}">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        @isset($header)
            <header class="border-bottom bg-white">
                <div class="container py-3">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="container py-4">
            {{ $slot }}
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script>
            const themeToggle = document.getElementById('themeToggle');
            const root = document.documentElement;
            const savedTheme = localStorage.getItem('habit-theme');
            if (savedTheme) {
                root.setAttribute('data-bs-theme', savedTheme);
            }
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const nextTheme = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                    root.setAttribute('data-bs-theme', nextTheme);
                    localStorage.setItem('habit-theme', nextTheme);
                });
            }
        </script>
        @stack('scripts')
    </body>
</html>
