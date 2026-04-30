@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <h2 id="page-title" class="h3">{{ $pageTitle }}</h2>
        <p class="mb-2">Welcome, <strong>{{ auth()->user()->name }}</strong>.</p>
        <p>
            Active role:
            <span class="badge text-bg-primary" aria-label="Active role badge">{{ $roleName }}</span>
        </p>
        <div class="alert alert-info mt-3" role="status">
            Phase 0 placeholder: Admin dashboard is ready for Phase 1 implementation.
        </div>
    </section>
@endsection
