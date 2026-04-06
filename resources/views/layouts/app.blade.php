<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=sora:400,500,600,700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

        <style>
            :root {
                --habit-accent: #1f7a5b;
                --habit-accent-strong: #155b44;
                --habit-card: #ffffff;
                --habit-bg: #eef3f1;
                --habit-ink: #1d2b25;
                --habit-muted: #6b7a73;
                --habit-border: rgba(18, 28, 24, 0.08);
                --habit-shadow: 0 20px 40px rgba(22, 31, 26, 0.08);
                --sidebar-width: 260px;
            }

            [data-bs-theme="dark"] {
                --habit-card: #1f2427;
                --habit-bg: #121416;
            }

            body {
                font-family: "Sora", sans-serif;
                color: var(--habit-ink);
                background: radial-gradient(circle at top left, #f8fbf9 0%, #edf3f1 35%, #e7edef 100%);
                min-height: 100vh;
            }

            body.dark-mode {
                background: #121212;
                color: #f1f1f1;
            }

            body.dark-mode .habit-card,
            body.dark-mode .page-header,
            body.dark-mode .sidebar,
            body.dark-mode .top-header,
            body.dark-mode .offcanvas {
                background: #1e1e1e;
                border-color: #2b2b2b;
                color: #f1f1f1;
            }

            body.dark-mode .text-muted {
                color: #b7b7b7 !important;
            }

            body.dark-mode .nav-link {
                color: #cfcfcf;
            }

            body.dark-mode .nav-link.active {
                color: #ffffff;
            }

            body.dark-mode .btn-icon,
            body.dark-mode .theme-toggle {
                background: #1e1e1e;
                color: #f1f1f1;
                border-color: #333;
            }

            body.dark-mode .table {
                background-color: #1e1e1e;
                color: #ffffff;
                --bs-table-bg: #1e1e1e;
                --bs-table-color: #e4e4e4;
                --bs-table-striped-bg: #242424;
                --bs-table-hover-bg: #2a2a2a;
                --bs-table-border-color: #3a3a3a;
            }

            body.dark-mode .table th {
                background-color: #2a2a2a !important;
                color: #e4e4e4 !important;
                border-color: #3a3a3a !important;
            }

            body.dark-mode .table td {
                border-color: #3a3a3a !important;
            }

            body.dark-mode .table thead th,
            body.dark-mode .table thead td {
                background-color: #2a2a2a !important;
                color: #e4e4e4 !important;
            }

            body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
                background-color: #242424 !important;
                color: #e4e4e4 !important;
            }

            body.dark-mode .table-hover > tbody > tr:hover > * {
                background-color: #2a2a2a !important;
                color: #ffffff !important;
            }

            body.dark-mode .table > :not(caption) > * > * {
                background-color: #1e1e1e;
                color: #e4e4e4;
            }

            body.dark-mode .form-control,
            body.dark-mode .form-select,
            body.dark-mode textarea,
            body.dark-mode input {
                background-color: #1e1e1e;
                color: #ffffff;
                border: 1px solid #444;
            }

            body.dark-mode .form-control::placeholder,
            body.dark-mode textarea::placeholder {
                color: #9a9a9a;
            }

            body.dark-mode .form-label {
                color: #e5e5e5;
            }

            .habit-status-label {
                min-width: 96px;
                text-align: right;
                font-size: 0.9rem;
                color: var(--habit-muted);
                transition: color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
            }

            .habit-status-label.is-complete {
                color: var(--habit-accent);
                font-weight: 600;
                transform: translateY(-1px);
            }

            .habit-toggle-wrap {
                position: relative;
            }

            .habit-toggle-input {
                position: absolute;
                opacity: 0;
                pointer-events: none;
            }

            .habit-toggle-control {
                width: 70px;
                height: 36px;
                border-radius: 999px;
                background: #cdd4d1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: relative;
                cursor: pointer;
                transition: background 0.25s ease, box-shadow 0.25s ease;
                box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
            }

            .habit-toggle-control .toggle-handle {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #fff;
                position: absolute;
                top: 4px;
                left: 5px;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                transition: transform 0.25s ease;
            }

            .habit-toggle-input:checked + .habit-toggle-control {
                background: #2fb179;
                box-shadow: 0 10px 20px rgba(47, 177, 121, 0.35);
            }

            .habit-toggle-input:checked + .habit-toggle-control .toggle-handle {
                transform: translateX(32px);
            }

            .habit-card {
                background: var(--habit-card);
                border: 1px solid var(--habit-border);
                box-shadow: var(--habit-shadow);
            }

            .habit-accent {
                color: var(--habit-accent);
            }

            .habit-gradient {
                background: linear-gradient(120deg, rgba(31, 122, 91, 0.12), rgba(18, 56, 42, 0.05));
            }

            .progress-bar {
                background-color: var(--habit-accent);
            }

            .nav-link {
                color: var(--habit-muted);
                font-weight: 600;
                border-radius: 999px;
                padding: 0.35rem 0.9rem;
                transition: all 0.2s ease;
            }

            .nav-link:hover {
                color: var(--habit-accent);
                background: rgba(31, 122, 91, 0.08);
            }

            .nav-link.active {
                color: #fff;
                background: var(--habit-accent);
                box-shadow: 0 10px 20px rgba(31, 122, 91, 0.25);
            }

            .btn-habit {
                background: var(--habit-accent);
                border: none;
                color: #fff;
                font-weight: 600;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .btn-habit:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 20px rgba(31, 122, 91, 0.25);
            }

            .btn-icon {
                width: 40px;
                height: 40px;
                border-radius: 999px;
                border: 1px solid var(--habit-border);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #fff;
                color: var(--habit-muted);
                transition: all 0.2s ease;
            }

            .btn-icon:hover {
                color: var(--habit-accent);
                border-color: rgba(31, 122, 91, 0.3);
                box-shadow: 0 10px 18px rgba(31, 122, 91, 0.15);
            }

            .page-header {
                background: #fff;
                border-radius: 1.25rem;
                border: 1px solid var(--habit-border);
                box-shadow: var(--habit-shadow);
            }

            .app-shell {
                display: flex;
                min-height: 100vh;
            }

            .sidebar {
                width: var(--sidebar-width);
                background: #ffffff;
                border-right: 1px solid var(--habit-border);
                padding: 1.5rem 1.25rem;
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1030;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .sidebar-brand {
                font-weight: 700;
                color: var(--habit-accent);
                text-decoration: none;
                font-size: 1.15rem;
            }

            .sidebar-nav .nav-link {
                width: 100%;
                justify-content: flex-start;
                padding: 0.55rem 0.9rem;
                border-radius: 0.85rem;
                display: inline-flex;
                gap: 0.75rem;
                align-items: center;
                font-size: 0.95rem;
            }

            .sidebar-nav .nav-link.active {
                color: #fff;
                background: var(--habit-accent);
                box-shadow: 0 12px 22px rgba(31, 122, 91, 0.25);
            }

            .content-area {
                flex: 1;
                margin-left: var(--sidebar-width);
                padding: 5.5rem 1.5rem 2.5rem;
            }

            .mobile-topbar {
                display: none;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.25rem;
                background: #ffffff;
                border-bottom: 1px solid var(--habit-border);
                position: sticky;
                top: 0;
                z-index: 1020;
            }

            .offcanvas-sidebar {
                width: 280px;
            }

            .top-header {
                position: fixed;
                top: 0;
                left: var(--sidebar-width);
                right: 0;
                height: 64px;
                background: #ffffff;
                border-bottom: 1px solid var(--habit-border);
                display: flex;
                align-items: center;
                padding: 0 1.5rem;
                z-index: 1040;
                gap: 1rem;
            }

            .header-title {
                font-weight: 700;
                color: var(--habit-accent);
                text-decoration: none;
            }

            .header-actions {
                margin-left: auto;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .theme-toggle {
                border: 1px solid var(--habit-border);
                border-radius: 999px;
                padding: 0.35rem 0.85rem;
                background: #fff;
                color: var(--habit-ink);
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.2s ease;
            }

            .theme-toggle:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 18px rgba(31, 122, 91, 0.12);
            }

            @media (max-width: 991.98px) {
                .sidebar {
                    display: none;
                }

                .top-header {
                    left: 0;
                    height: 60px;
                    padding: 0 1.25rem;
                }

                .content-area {
                    margin-left: 0;
                    padding: 4.75rem 1.25rem 2rem;
                }

                .mobile-topbar {
                    display: flex;
                }
            }
        </style>
        @stack('styles')
    </head>
    <body>
        @auth
            <div class="top-header">
                <button class="btn-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <a class="header-title" href="{{ route('dashboard') }}">Habit Tracker</a>
                <div class="header-actions">
                    <span class="small text-muted d-none d-md-inline">{{ Auth::user()->name }}</span>
                    <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle theme">
                        <span class="theme-icon">☀️</span>
                        <span class="d-none d-sm-inline">Theme</span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button class="btn-icon" type="submit" aria-label="Log out" title="Log out">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endauth

        <div class="app-shell">
            @auth
                <aside class="sidebar">
                    <div>
                        <a class="sidebar-brand" href="{{ route('dashboard') }}">Habit Tracker</a>
                        <p class="text-muted small mt-2 mb-0">Build momentum every day.</p>
                    </div>
                    <nav class="sidebar-nav nav flex-column gap-2">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-house"></i>Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('statistics') ? 'active' : '' }}" href="{{ route('statistics') }}">
                            <i class="fa-solid fa-chart-pie"></i>Statistics
                        </a>
                        <a class="nav-link {{ request()->routeIs('habits.create') ? 'active' : '' }}" href="{{ route('habits.create') }}">
                            <i class="fa-solid fa-plus"></i>Add Habit
                        </a>
                        <a class="nav-link {{ request()->routeIs('habits.manage', 'habits.edit') ? 'active' : '' }}" href="{{ route('habits.manage') }}">
                            <i class="fa-solid fa-list-check"></i>Manage Habits
                        </a>
                    </nav>
                </aside>

                <div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="mobileSidebar">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title habit-accent">Habit Tracker</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        <nav class="sidebar-nav nav flex-column gap-2">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fa-solid fa-house"></i>Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('statistics') ? 'active' : '' }}" href="{{ route('statistics') }}">
                                <i class="fa-solid fa-chart-pie"></i>Statistics
                            </a>
                            <a class="nav-link {{ request()->routeIs('habits.create') ? 'active' : '' }}" href="{{ route('habits.create') }}">
                                <i class="fa-solid fa-plus"></i>Add Habit
                            </a>
                            <a class="nav-link {{ request()->routeIs('habits.manage', 'habits.edit') ? 'active' : '' }}" href="{{ route('habits.manage') }}">
                                <i class="fa-solid fa-list-check"></i>Manage Habits
                            </a>
                        </nav>
                    </div>
                </div>
            @endauth

            <div class="content-area">
                @isset($header)
                    <header class="mb-4">
                        <div class="page-header px-4 py-3">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script>
            const themeToggle = document.getElementById('themeToggle');
            const body = document.body;
            const storedTheme = localStorage.getItem('habit-theme');
            if (storedTheme === 'dark') {
                body.classList.add('dark-mode');
            }

            function syncThemeIcon() {
                if (!themeToggle) return;
                const icon = themeToggle.querySelector('.theme-icon');
                if (!icon) return;
                icon.textContent = body.classList.contains('dark-mode') ? '🌙' : '☀️';
            }

            syncThemeIcon();

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    body.classList.toggle('dark-mode');
                    localStorage.setItem('habit-theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
                    syncThemeIcon();
                });
            }
        </script>
        @stack('scripts')
    </body>
</html>
