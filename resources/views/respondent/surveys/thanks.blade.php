@extends('layouts.public')

@section('content')
    <section aria-labelledby="thank-you-title">
        <div class="card af-card">
            <div class="card-body">
                <h2 id="thank-you-title" class="h3 mb-2">Submission received</h2>
                <p class="af-muted">Thank you for completing {{ $survey->title }}.</p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('respondent.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                    <a href="{{ route('surveys.public.show', $survey->public_slug) }}" class="btn btn-outline-secondary">Submit another response</a>
                </div>
            </div>
        </div>
    </section>
@endsection
