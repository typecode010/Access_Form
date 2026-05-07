@extends('layouts.role-app')

@section('content')
    <section class="af-dashboard" aria-labelledby="page-title">
        <div class="af-page-intro">
            <h2 id="page-title" class="h3 mb-0">{{ $pageTitle }}</h2>
            <p class="mb-0">Welcome, <strong>{{ auth()->user()->name }}</strong>.</p>
            <p class="af-muted mb-0">
                Active role:
                <span class="badge text-bg-success" aria-label="Active role badge">{{ $roleName }}</span>
            </p>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card af-card">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <h3 class="h5 af-section-title" id="quick-actions-title">Quick Actions</h3>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#quick-actions-panel"
                                aria-expanded="true"
                                aria-controls="quick-actions-panel"
                            >
                                Toggle
                            </button>
                        </div>
                        <div id="quick-actions-panel" class="collapse show" aria-labelledby="quick-actions-title">
                            <div class="af-action-group">
                                <a class="btn btn-primary" href="{{ route('creator.surveys.create') }}">Create New Survey</a>
                                <a class="btn btn-outline-secondary" href="{{ route('creator.surveys.index') }}">My Surveys</a>
                                @if ($latestSurvey)
                                    <a class="btn btn-outline-success" href="{{ route('creator.surveys.accessibility.edit', $latestSurvey) }}">Accessibility Settings</a>
                                @else
                                    <button class="btn btn-outline-success" type="button" disabled aria-disabled="true">Accessibility Settings</button>
                                @endif
                                <button class="btn btn-outline-dark" type="button" disabled aria-disabled="true">View Reports</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        @include('creator.partials._stat_card', [
                            'label' => 'Total surveys',
                            'value' => $totalSurveys,
                            'helper' => 'All surveys you have created.',
                            'cardId' => 'stat-total',
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('creator.partials._stat_card', [
                            'label' => 'Draft surveys',
                            'value' => $draftSurveys,
                            'helper' => 'Not published yet.',
                            'cardId' => 'stat-draft',
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('creator.partials._stat_card', [
                            'label' => 'Published surveys',
                            'value' => $publishedSurveys,
                            'helper' => 'Visible to respondents.',
                            'cardId' => 'stat-published',
                        ])
                    </div>
                </div>

                <div class="card af-card mt-4">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <div>
                                <h3 class="h5 af-section-title" id="survey-snapshot-title">My Surveys Snapshot</h3>
                                <p class="af-muted mb-0">Quick access to your most recent surveys.</p>
                            </div>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#survey-snapshot-panel"
                                aria-expanded="true"
                                aria-controls="survey-snapshot-panel"
                            >
                                Toggle
                            </button>
                        </div>

                        <div id="survey-snapshot-panel" class="collapse show" aria-labelledby="survey-snapshot-title">
                            @if ($latestSurvey)
                                <div class="alert alert-light border" role="status">
                                    Last updated: <strong>{{ $latestSurvey->title }}</strong>
                                    <span class="af-muted">({{ $latestSurvey->updated_at?->diffForHumans() }})</span>
                                </div>
                            @endif

                            <div class="mb-3 af-filter">
                                <label for="survey-filter" class="form-label">Filter surveys</label>
                                <input
                                    id="survey-filter"
                                    type="text"
                                    class="form-control"
                                    placeholder="Search by title, slug, or status"
                                    data-survey-filter
                                    aria-describedby="survey-filter-help survey-filter-status"
                                >
                                <div id="survey-filter-help" class="form-text">Filtering only affects this list.</div>
                                <div id="survey-filter-status" class="visually-hidden" role="status" aria-live="polite" data-survey-filter-status></div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle af-table" id="survey-snapshot-table">
                                    <caption class="visually-hidden">Creator survey snapshot list</caption>
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Survey</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Accessibility</th>
                                            <th scope="col">Updated</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($surveys as $survey)
                                            @include('creator.partials._survey_row', [
                                                'survey' => $survey,
                                                'warningCount' => $surveyWarningCounts[$survey->id] ?? 0,
                                            ])
                                        @empty
                                            <tr data-survey-empty>
                                                <td colspan="5" class="text-center py-4">No surveys yet. Click "Create New Survey" to start.</td>
                                            </tr>
                                        @endforelse
                                        @if ($surveys->isNotEmpty())
                                            <tr data-survey-empty class="d-none">
                                                <td colspan="5" class="text-center py-4">No surveys match your filter.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card af-card">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <h3 class="h5 af-section-title" id="a11y-summary-title">Accessibility Status</h3>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#a11y-summary-panel"
                                aria-expanded="true"
                                aria-controls="a11y-summary-panel"
                            >
                                Toggle
                            </button>
                        </div>
                        <div id="a11y-summary-panel" class="collapse show" aria-labelledby="a11y-summary-title">
                            <p class="af-muted mb-2">Surveys with warnings: <strong>{{ $warningSummary['surveysWithWarnings'] }}</strong></p>

                            @if (count($warningSummary['topWarnings']) > 0)
                                <ul class="af-list">
                                    @foreach ($warningSummary['topWarnings'] as $warning)
                                        <li class="af-list-item">
                                            <span>{{ $warning['label'] }}</span>
                                            <span class="badge text-bg-warning">{{ $warning['count'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mb-0 text-success">No warnings detected in recent surveys.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card af-card mt-4">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <h3 class="h5 af-section-title" id="activity-title">Recent Activity</h3>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#activity-panel"
                                aria-expanded="true"
                                aria-controls="activity-panel"
                            >
                                Toggle
                            </button>
                        </div>
                        <div id="activity-panel" class="collapse show" aria-labelledby="activity-title">
                            @if ($recentActivity->isEmpty())
                                <p class="mb-0 af-muted">No recent survey activity yet.</p>
                            @else
                                <ul class="af-list">
                                    @foreach ($recentActivity as $activity)
                                        <li class="af-list-item">
                                            <div>
                                                <strong>{{ $activity['action'] }}</strong>: {{ $activity['title'] }}
                                            </div>
                                            <span class="af-muted small">{{ $activity['time']?->diffForHumans() }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
