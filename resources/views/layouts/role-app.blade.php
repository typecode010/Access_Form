<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle ?? config('app.name', 'AccessForm') }}</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
            crossorigin="anonymous"
        >

        <style>
            .skip-link {
                left: 0.75rem;
                position: absolute;
                top: -3rem;
                z-index: 9999;
            }

            .skip-link:focus {
                top: 0.75rem;
            }

            :focus-visible {
                outline: 3px solid #ffbf47 !important;
                outline-offset: 2px !important;
            }
        </style>
    </head>
    <body class="bg-light text-dark">
        <a class="btn btn-warning skip-link" href="#main-content">Skip to main content</a>

        <header class="border-bottom bg-white" role="banner">
            <div class="container py-3 d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">{{ config('app.name', 'AccessForm') }}</h1>
                <div class="text-muted" aria-label="Logged in user">
                    {{ auth()->user()->name }}
                </div>
            </div>
        </header>

        <nav class="navbar navbar-expand-lg navbar-light bg-body" role="navigation" aria-label="Primary navigation">
            <div class="container">
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#primaryNav"
                    aria-controls="primaryNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="primaryNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @if(auth()->user()->hasRole('Admin'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">Manage Users</a>
                            </li>
                        @endif

                        @if(auth()->user()->hasRole('FormCreator'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('creator.dashboard') }}">Creator Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('creator.surveys.index') }}">My Surveys</a>
                            </li>
                        @endif

                        @if(auth()->user()->hasRole('Respondent'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('respondent.dashboard') }}">Respondent Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('respondent.surveys.index') }}">Available Surveys</a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">Profile</a>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Log Out</button>
                    </form>
                </div>
            </div>
        </nav>

        <main id="main-content" class="container py-4" role="main" tabindex="-1">
            @yield('content')
        </main>

        <footer class="border-top bg-white py-3" role="contentinfo">
            <div class="container text-muted small">
                AccessForm Phase 0 Placeholder UI
            </div>
        </footer>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
