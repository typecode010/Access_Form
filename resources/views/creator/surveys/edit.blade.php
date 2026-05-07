@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2">
            <h2 id="page-title" class="h3 mb-0">{{ $pageTitle }}</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('creator.surveys.media.index', $survey) }}" class="btn btn-outline-primary">Manage Media</a>
                <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-primary">Question Builder</a>
                <a href="{{ route('creator.surveys.index') }}" class="btn btn-outline-secondary">Back to Surveys</a>
            </div>
        </div>

        <p class="text-muted mb-3">
            Public slug:
            <code>{{ $survey->public_slug }}</code>
        </p>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="polite">
                Please review the highlighted fields and try again.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('creator.surveys.update', $survey) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('creator.surveys._form', ['survey' => $survey])
                </form>
            </div>
        </div>
    </section>
@endsection
