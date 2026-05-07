@extends('layouts.role-app')

@section('content')
    @php
        $totalResponses = $responses->total();
    @endphp

    <section class="af-dashboard respondent-dashboard respondent-theme" data-respondent-theme aria-labelledby="page-title">
        <div class="af-page-intro">
            <h2 id="page-title" class="h3 mb-0">{{ $pageTitle }}</h2>
            <p class="af-muted mb-0">Track surveys you have completed on AccessForm.</p>
        </div>

        <div class="card af-card respondent-highlight">
            <div class="card-body">
                <div class="af-card-header mb-3">
                    <div>
                        <h3 class="h5 af-section-title" id="submissions-title">My submissions</h3>
                        <p class="af-muted mb-0">Total submissions: <strong>{{ $totalResponses }}</strong></p>
                    </div>
                    <div class="af-action-group">
                        <a href="{{ route('respondent.surveys.index') }}" class="btn btn-primary">Browse surveys</a>
                        <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                    </div>
                </div>

                @if ($latestSubmission)
                    <div class="alert alert-light border" role="status">
                        Latest submission: <strong>{{ $latestSubmission->survey?->title ?? 'Survey' }}</strong>
                        <span class="af-muted">({{ $latestSubmission->submitted_at?->diffForHumans() ?? 'In progress' }})</span>
                    </div>
                @endif

                @if ($responses->count() === 0)
                    <p class="af-muted mb-0">No submissions yet. Start a survey from the available list.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle af-table">
                            <caption class="visually-hidden">My submissions list</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Survey</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Submitted</th>
                                    <th scope="col">Answers</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($responses as $response)
                                    @include('respondent.partials._submission_row', ['response' => $response])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $responses->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
