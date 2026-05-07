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
        </style>
    </head>
    <body class="bg-light text-dark">
        <a class="btn btn-warning skip-link" href="#main-content">Skip to main content</a>

        <header class="border-bottom bg-white" role="banner">
            <div class="container py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h4 mb-0">{{ config('app.name', 'AccessForm') }}</h1>
                    <p class="text-muted mb-0">Accessible survey experience</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Log in</a>
                    @endauth
                </div>
            </div>
        </header>

        <main id="main-content" class="container py-4" role="main" tabindex="-1">
            @yield('content')
        </main>

        <footer class="border-top bg-white py-3" role="contentinfo">
            <div class="container text-muted small">
                AccessForm public survey portal
            </div>
        </footer>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
