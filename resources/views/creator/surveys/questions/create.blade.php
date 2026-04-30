@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-outline-secondary">Back to Questions</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="polite">
                Please review the highlighted fields and try again.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('creator.surveys.questions.store', $survey) }}" novalidate>
                    @csrf
                    @include('creator.surveys.questions._form', [
                        'survey' => $survey,
                        'question' => $question,
                        'questionTypes' => $questionTypes,
                        'submitLabel' => 'Create Question',
                    ])
                </form>
            </div>
        </div>
    </section>
@endsection
