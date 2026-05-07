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

        @vite(['resources/js/app.js'])

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

            .preview-theme.theme-contrast {
                background: #0b0b0b;
                border-color: #f5f5f5;
                color: #ffffff;
            }

            .preview-theme.theme-contrast .form-control,
            .preview-theme.theme-contrast .form-select {
                background: #111111;
                border-color: #f5f5f5;
                color: #ffffff;
            }

            .preview-theme.theme-contrast .form-check-input {
                border-color: #f5f5f5;
            }

            .preview-theme.theme-contrast .text-muted {
                color: #d9d9d9 !important;
            }

            .preview-theme.theme-dyslexia {
                font-family: Arial, Verdana, Tahoma, sans-serif;
                letter-spacing: 0.04em;
                line-height: 1.6;
            }

            .preview-theme.text-size-sm {
                font-size: 0.95rem;
            }

            .preview-theme.text-size-md {
                font-size: 1rem;
            }

            .preview-theme.text-size-lg {
                font-size: 1.125rem;
            }

            .preview-theme.reduced-motion *,
            .preview-theme.reduced-motion *::before,
            .preview-theme.reduced-motion *::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.001ms !important;
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
                                <a
                                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('admin.dashboard') }}"
                                    @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif
                                >
                                    Admin Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}"
                                    @if (request()->routeIs('admin.users.*')) aria-current="page" @endif
                                >
                                    Manage Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('admin.accessibility.issues.*') ? 'active' : '' }}"
                                    href="{{ route('admin.accessibility.issues.index') }}"
                                    @if (request()->routeIs('admin.accessibility.issues.*')) aria-current="page" @endif
                                >
                                    Accessibility Issues
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->hasRole('FormCreator'))
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('creator.dashboard') ? 'active' : '' }}"
                                    href="{{ route('creator.dashboard') }}"
                                    @if (request()->routeIs('creator.dashboard')) aria-current="page" @endif
                                >
                                    Creator Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('creator.surveys.*') ? 'active' : '' }}"
                                    href="{{ route('creator.surveys.index') }}"
                                    @if (request()->routeIs('creator.surveys.*')) aria-current="page" @endif
                                >
                                    My Surveys
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->hasRole('Respondent'))
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('respondent.dashboard') ? 'active' : '' }}"
                                    href="{{ route('respondent.dashboard') }}"
                                    @if (request()->routeIs('respondent.dashboard')) aria-current="page" @endif
                                >
                                    Respondent Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('respondent.surveys.*') ? 'active' : '' }}"
                                    href="{{ route('respondent.surveys.index') }}"
                                    @if (request()->routeIs('respondent.surveys.*')) aria-current="page" @endif
                                >
                                    Available Surveys
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link {{ request()->routeIs('respondent.submissions.*') ? 'active' : '' }}"
                                    href="{{ route('respondent.submissions.index') }}"
                                    @if (request()->routeIs('respondent.submissions.*')) aria-current="page" @endif
                                >
                                    My Submissions
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a
                                class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                                href="{{ route('profile.edit') }}"
                                @if (request()->routeIs('profile.edit')) aria-current="page" @endif
                            >
                                Profile
                            </a>
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
