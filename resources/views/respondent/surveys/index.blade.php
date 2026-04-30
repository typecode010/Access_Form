@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <h2 id="page-title" class="h3">{{ $pageTitle }}</h2>
        <p class="mb-2">Welcome, <strong>{{ auth()->user()->name }}</strong>.</p>
        <p>
            Active role:
            <span class="badge text-bg-dark" aria-label="Active role badge">{{ $roleName }}</span>
        </p>
        <div class="alert alert-info mt-3" role="status">
            Phase 1 starter: Public survey discovery and response flow will be added next.
        </div>
    </section>
@endsection
