<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AccessForm | Accessibility-First Survey Builder</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header border-bottom" role="banner">
        <nav class="navbar navbar-expand-lg" aria-label="Primary">
            <div class="container py-2">
                <a class="navbar-brand brand-mark" href="{{ url('/') }}">AccessForm</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="primaryNav">
                    <ul class="navbar-nav ms-auto me-3 gap-lg-2">
                        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                        <li class="nav-item"><a class="nav-link" href="#accessibility">Accessibility</a></li>
                        <li class="nav-item"><a class="nav-link" href="#roles">Roles</a></li>
                    </ul>

                    @if (Route::has('login'))
                        @auth
                            <a class="btn btn-primary" href="{{ url('/dashboard') }}">Go to Dashboard</a>
                        @else
                            <div class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-outline-primary" href="{{ route('login') }}">Log In</a>
                                @if (Route::has('register'))
                                    <a class="btn btn-primary" href="{{ route('register') }}">Create Account</a>
                                @endif
                            </div>
                        @endauth
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <main id="main-content" tabindex="-1">
        <section class="hero-section py-5 py-lg-6" aria-labelledby="hero-title">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <p class="eyebrow">Final Year Project • Laravel 11 • Bootstrap 5</p>
                        <h1 id="hero-title" class="display-5 fw-bold mb-3">
                            Build Surveys Everyone Can Access
                        </h1>
                        <p class="lead mb-4">
                            AccessForm is an accessibility-first survey builder designed for inclusive participation across visual,
                            hearing, motor, and cognitive needs.
                        </p>

                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <span class="pill-tag">WCAG 2.1 Focus</span>
                            <span class="pill-tag">Keyboard-First UX</span>
                            <span class="pill-tag">Screen Reader Friendly</span>
                        </div>

                        @if (Route::has('login'))
                            @auth
                                <a class="btn btn-lg btn-primary me-2" href="{{ url('/dashboard') }}">Continue to Dashboard</a>
                            @else
                                <a class="btn btn-lg btn-primary me-2" href="{{ route('register') }}">Start Building</a>
                                <a class="btn btn-lg btn-outline-primary" href="{{ route('login') }}">Log In</a>
                            @endauth
                        @endif
                    </div>

                    <div class="col-lg-5">
                        <div class="hero-panel">
                            <h2 class="h4 mb-3">Project Promise</h2>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3"><strong>Inclusive Form Design:</strong> creators get accessibility checks before publish.</li>
                                <li class="mb-3"><strong>Multi-Channel Responses:</strong> web-first with voice and SMS adaptation path.</li>
                                <li><strong>Accessible Reporting:</strong> table-first analytics with export support.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="py-5" aria-labelledby="features-title">
            <div class="container">
                <div class="section-head mb-4">
                    <h2 id="features-title" class="h2 mb-2">Core Features</h2>
                    <p class="mb-0">Aligned with your project functional requirements and MVP scope.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card h-100">
                            <h3 class="h5">Survey Builder</h3>
                            <p class="mb-0">Create forms with multiple choice, text input, rating scales, and file uploads.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card h-100">
                            <h3 class="h5">Accessibility Engine</h3>
                            <p class="mb-0">Keyboard-only navigation, ARIA support, high-contrast mode, and dyslexia-friendly options.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card h-100">
                            <h3 class="h5">Response Channels</h3>
                            <p class="mb-0">Collect responses through web with adaptation path for voice and SMS participation.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card h-100">
                            <h3 class="h5">Analytics and Export</h3>
                            <p class="mb-0">Accessible dashboards with export options for CSV, Excel, and PDF workflows.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="accessibility" class="py-5" aria-labelledby="accessibility-title">
            <div class="container">
                <div class="section-head mb-4">
                    <h2 id="accessibility-title" class="h2 mb-2">Accessibility Commitments</h2>
                    <p class="mb-0">Built around WCAG 2.1 principles: Perceivable, Operable, Understandable, and Robust.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-xl-3">
                        <article class="principle-card h-100">
                            <h3 class="h6 text-uppercase">Perceivable</h3>
                            <p class="mb-0">Text alternatives, clear labels, and meaningful visual hierarchy.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="principle-card h-100">
                            <h3 class="h6 text-uppercase">Operable</h3>
                            <p class="mb-0">Strong focus states, keyboard support, and no keyboard traps.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="principle-card h-100">
                            <h3 class="h6 text-uppercase">Understandable</h3>
                            <p class="mb-0">Consistent navigation, clear errors, and readable content flow.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="principle-card h-100">
                            <h3 class="h6 text-uppercase">Robust</h3>
                            <p class="mb-0">Semantic markup with assistive technology compatibility.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="roles" class="py-5" aria-labelledby="roles-title">
            <div class="container">
                <div class="section-head mb-4">
                    <h2 id="roles-title" class="h2 mb-2">Built for 3 User Roles</h2>
                    <p class="mb-0">Role-based access helps keep workflows focused and secure.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <article class="role-card h-100">
                            <h3 class="h5">Admin</h3>
                            <p class="mb-0">Manages users, monitors surveys, and tracks accessibility compliance.</p>
                        </article>
                    </div>
                    <div class="col-md-4">
                        <article class="role-card h-100">
                            <h3 class="h5">Form Creator</h3>
                            <p class="mb-0">Builds surveys, configures accessibility settings, and publishes forms.</p>
                        </article>
                    </div>
                    <div class="col-md-4">
                        <article class="role-card h-100">
                            <h3 class="h5">Respondent</h3>
                            <p class="mb-0">Completes surveys through web and future-friendly adaptive channels.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer py-4" role="contentinfo">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <p class="mb-0">AccessForm • Accessibility-First Survey Builder</p>
            <p class="mb-0">Laravel {{ Illuminate\Foundation\Application::VERSION }} • PHP {{ PHP_VERSION }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
