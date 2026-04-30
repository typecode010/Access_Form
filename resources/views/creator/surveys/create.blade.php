@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <h2 id="page-title" class="h3">{{ $pageTitle }}</h2>
        <p class="text-muted">Start with basic survey details. Question builder will be connected next.</p>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('creator.surveys.store') }}" novalidate>
                    @csrf
                    @include('creator.surveys._form', ['survey' => $survey])
                </form>
            </div>
        </div>
    </section>
@endsection
