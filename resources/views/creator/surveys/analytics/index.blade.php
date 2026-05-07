@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="af-muted mb-0">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <div class="af-action-group">
                <form method="POST" action="{{ route('creator.surveys.exports.csv', $survey) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">Generate CSV</button>
                </form>
                <a href="{{ route('creator.surveys.responses.index', $survey) }}" class="btn btn-outline-info">View Responses</a>
                <a href="{{ route('creator.surveys.show', $survey) }}" class="btn btn-outline-secondary">Back to Survey</a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                @include('creator.partials._stat_card', [
                    'label' => 'Total responses',
                    'value' => $totalResponses,
                    'helper' => 'Completed survey submissions.',
                    'cardId' => 'analytics-total',
                ])
            </div>
            <div class="col-md-4">
                @include('creator.partials._stat_card', [
                    'label' => 'Latest response',
                    'value' => $latestResponse?->submitted_at?->diffForHumans() ?? 'N/A',
                    'helper' => 'Most recent submission time.',
                    'cardId' => 'analytics-latest',
                ])
            </div>
            <div class="col-md-4">
                @include('creator.partials._stat_card', [
                    'label' => 'Questions',
                    'value' => $survey->questions->count(),
                    'helper' => 'Active questions in this survey.',
                    'cardId' => 'analytics-questions',
                ])
            </div>
        </div>

        @if ($responsesPerDay->isNotEmpty())
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Responses per day</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <caption class="visually-hidden">Responses per day</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Responses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($responsesPerDay as $row)
                                    <tr>
                                        <td>{{ $row->day }}</td>
                                        <td>{{ $row->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h3 class="h5 mb-3">Question summaries</h3>

                @if ($questionSummaries->isEmpty())
                    <p class="text-muted mb-0">No questions have been added yet.</p>
                @else
                    <div class="vstack gap-3">
                        @foreach ($questionSummaries as $summary)
                            @php($question = $summary['question'])
                            <article class="border rounded p-3" aria-labelledby="question-summary-{{ $question->id }}">
                                <div class="d-flex flex-wrap justify-content-between gap-2">
                                    <h4 id="question-summary-{{ $question->id }}" class="h6 mb-1">
                                        Q{{ $question->position }}. {{ $question->prompt ?: 'Untitled question' }}
                                    </h4>
                                    <span class="badge text-bg-secondary text-uppercase">{{ $summary['type'] }}</span>
                                </div>
                                <p class="text-muted mb-2">Total answers: {{ $summary['answer_count'] }}</p>

                                @if ($summary['type'] === 'text')
                                    <div>
                                        <strong>Latest samples:</strong>
                                        @if (empty($summary['latest_samples']))
                                            <p class="text-muted mb-0">No text responses yet.</p>
                                        @else
                                            <ul class="mb-0">
                                                @foreach ($summary['latest_samples'] as $sample)
                                                    <li>{{ $sample }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @elseif ($summary['type'] === 'multiple_choice')
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <caption class="visually-hidden">Option counts</caption>
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">Option</th>
                                                    <th scope="col">Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($summary['option_counts'] as $option)
                                                    <tr>
                                                        <td>{{ $option['label'] }}</td>
                                                        <td>{{ $option['count'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted">No option data.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @elseif ($summary['type'] === 'rating')
                                    <div class="d-flex flex-wrap gap-3">
                                        <div><strong>Average:</strong> {{ $summary['rating_stats']['avg'] ?? 'N/A' }}</div>
                                        <div><strong>Min:</strong> {{ $summary['rating_stats']['min'] ?? 'N/A' }}</div>
                                        <div><strong>Max:</strong> {{ $summary['rating_stats']['max'] ?? 'N/A' }}</div>
                                    </div>
                                @elseif ($summary['type'] === 'file')
                                    <div><strong>Files uploaded:</strong> {{ $summary['file_count'] }}</div>
                                @else
                                    <p class="text-muted mb-0">No summary available for this question type.</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="h5 mb-3">Recent exports</h3>
                @if ($exports->isEmpty())
                    <p class="text-muted mb-0">No exports generated yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <caption class="visually-hidden">Exports list</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Generated</th>
                                    <th scope="col">Format</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exports as $export)
                                    <tr>
                                        <td>{{ $export->generated_at?->toDayDateTimeString() ?? $export->created_at?->toDayDateTimeString() }}</td>
                                        <td class="text-uppercase">{{ $export->format }}</td>
                                        <td>{{ $export->status }}</td>
                                        <td>
                                            @if ($export->file_path)
                                                <a href="{{ route('creator.exports.download', $export) }}" class="btn btn-sm btn-outline-primary">Download</a>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
