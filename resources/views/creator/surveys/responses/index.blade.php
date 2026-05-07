@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="af-muted mb-0">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <div class="af-action-group">
                <a href="{{ route('creator.surveys.analytics.show', $survey) }}" class="btn btn-outline-primary">Analytics & Exports</a>
                <a href="{{ route('creator.surveys.show', $survey) }}" class="btn btn-outline-primary">Back to Survey</a>
            </div>
        </div>

        <div class="alert alert-info" role="status">
            Use the analytics page to generate CSV exports.
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                @include('creator.partials._stat_card', [
                    'label' => 'Total responses',
                    'value' => $totalResponses,
                    'helper' => 'Submitted survey responses.',
                    'cardId' => 'responses-total',
                ])
            </div>
            <div class="col-md-4">
                @include('creator.partials._stat_card', [
                    'label' => 'Latest response',
                    'value' => $latestResponse?->submitted_at?->diffForHumans() ?? 'N/A',
                    'helper' => 'Most recent submission time.',
                    'cardId' => 'responses-latest',
                ])
            </div>
            <div class="col-md-4">
                @include('creator.partials._stat_card', [
                    'label' => 'Channel',
                    'value' => 'Web',
                    'helper' => 'Voice/SMS coming soon.',
                    'cardId' => 'responses-channel',
                ])
            </div>
        </div>

        @if ($responses->count() === 0)
            <p class="af-muted">No responses yet. Share your survey to collect responses.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle af-table">
                    <caption class="visually-hidden">Responses for {{ $survey->title }}</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Respondent</th>
                            <th scope="col">Submitted</th>
                            <th scope="col">Answers</th>
                            <th scope="col">Channel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($responses as $response)
                            <tr>
                                <td>
                                    @if ($response->respondent)
                                        <div class="fw-semibold">{{ $response->respondent->name }}</div>
                                        <div class="af-muted small">{{ $response->respondent->email }}</div>
                                    @else
                                        <span class="af-muted">Anonymous</span>
                                    @endif
                                </td>
                                <td>{{ $response->submitted_at?->toDayDateTimeString() ?? 'In progress' }}</td>
                                <td>{{ $response->answers_count }}</td>
                                <td>{{ $response->channel }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $responses->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
@endsection
