<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AccessForm | Register</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header border-bottom" role="banner">
        <nav class="navbar navbar-expand-lg" aria-label="Authentication navigation">
            <div class="container py-2">
                <a class="navbar-brand brand-mark" href="{{ url('/') }}">AccessForm</a>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary" href="{{ url('/') }}">Home</a>
                    <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </nav>
    </header>

    <main id="main-content" class="py-5" tabindex="-1">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-6 d-flex">
                    <section class="auth-info-panel w-100" aria-labelledby="register-title">
                        <p class="eyebrow mb-2">Create Your Workspace</p>
                        <h1 id="register-title" class="display-6 fw-bold mb-3">Join AccessForm</h1>
                        <p class="mb-4">
                            Register to start building inclusive surveys with accessibility checks from day one.
                        </p>

                        <ul class="list-unstyled auth-point-list mb-0">
                            <li>Role-based access for structured platform workflows</li>
                            <li>Accessible interface for keyboard and assistive technology users</li>
                            <li>Ready for survey creation, response collection, and reporting</li>
                        </ul>
                    </section>
                </div>

                <div class="col-lg-6 d-flex">
                    <section class="auth-form-panel w-100" aria-labelledby="register-card-title">
                        <h2 id="register-card-title" class="h3 mb-3">Register</h2>

                        <form method="POST" action="{{ route('register') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full name</label>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="username"
                                    class="form-control @error('email') is-invalid @enderror"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Register as</label>
                                <select
                                    id="role"
                                    name="role"
                                    required
                                    class="form-select @error('role') is-invalid @enderror"
                                    aria-describedby="role-help"
                                >
                                    <option value="">Select a role</option>
                                    <option value="FormCreator" @selected(old('role') === 'FormCreator')>Form Creator</option>
                                    <option value="Respondent" @selected(old('role') === 'Respondent')>Respondent</option>
                                </select>
                                <div id="role-help" class="form-text">
                                    Choose your platform role for this account.
                                </div>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    class="form-control @error('password') is-invalid @enderror"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm password</label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                >
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('login') }}" class="auth-inline-link">Already registered? Log in</a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <footer class="site-footer py-4" role="contentinfo">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <p class="mb-0">AccessForm • Inclusive Authentication Experience</p>
            <p class="mb-0">Laravel {{ Illuminate\Foundation\Application::VERSION }} • PHP {{ PHP_VERSION }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
