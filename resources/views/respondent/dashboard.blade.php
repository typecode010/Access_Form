@extends('layouts.role-app')

@section('content')
    <section class="af-dashboard respondent-dashboard respondent-theme" data-respondent-theme aria-labelledby="page-title">
        <div class="af-page-intro">
            <h2 id="page-title" class="h3 mb-0">{{ $pageTitle }}</h2>
            <p class="mb-0">Welcome, <strong>{{ auth()->user()->name }}</strong>.</p>
            <p class="af-muted mb-0">
                Active role:
                <span class="badge text-bg-dark" aria-label="Active role badge">{{ $roleName }}</span>
            </p>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card af-card respondent-highlight">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <h3 class="h5 af-section-title" id="quick-access-title">Quick Access</h3>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#quick-access-panel"
                                aria-expanded="true"
                                aria-controls="quick-access-panel"
                            >
                                Toggle
                            </button>
                        </div>

                        <div id="quick-access-panel" class="collapse show" aria-labelledby="quick-access-title">
                            <form class="row g-3" data-slug-form>
                                <div class="col-md-7">
                                    <label for="survey-slug" class="form-label">Enter survey link or slug</label>
                                    <input
                                        id="survey-slug"
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g. accessibility-demo-survey"
                                        data-slug-input
                                        aria-describedby="survey-slug-help survey-slug-status"
                                    >
                                    <div id="survey-slug-help" class="form-text">You will be redirected to the survey page.</div>
                                    <div id="survey-slug-status" class="visually-hidden" role="status" aria-live="polite" data-slug-status></div>
                                </div>
                                <div class="col-md-5 d-flex align-items-end gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary">Go</button>
                                    <a href="#available-surveys" class="btn btn-outline-secondary">Browse available surveys</a>
                                    <a class="btn btn-outline-dark" href="{{ route('respondent.submissions.index') }}">My submissions</a>
                                </div>
                            </form>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button class="btn btn-outline-secondary" type="button" disabled aria-disabled="true">Voice participation (soon)</button>
                                <button class="btn btn-outline-secondary" type="button" disabled aria-disabled="true">SMS participation (soon)</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card af-card mt-4" id="available-surveys">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <div>
                                <h3 class="h5 af-section-title" id="available-surveys-title">Available Surveys</h3>
                                <p class="af-muted mb-0">Explore published surveys you can complete.</p>
                            </div>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#available-surveys-panel"
                                aria-expanded="true"
                                aria-controls="available-surveys-panel"
                            >
                                Toggle
                            </button>
                        </div>

                        <div id="available-surveys-panel" class="collapse show" aria-labelledby="available-surveys-title">
                            @include('respondent.partials._filter_bar', ['total' => $surveys->count()])

                            @if ($surveys->count() === 0)
                                <p class="af-muted">No published surveys are available yet.</p>
                            @else
                                <div class="respondent-survey-grid" data-survey-container>
                                    @foreach ($surveys as $survey)
                                        @include('respondent.partials._survey_card', ['survey' => $survey])
                                    @endforeach
                                </div>
                                <div class="mt-3 d-none" data-survey-empty>
                                    <p class="af-muted mb-0">No surveys match your filters.</p>
                                </div>
                                <div class="mt-3">
                                    {{ $surveys->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card af-card">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <h3 class="h5 af-section-title" id="recent-activity-title">Continue / Recent Activity</h3>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#recent-activity-panel"
                                aria-expanded="true"
                                aria-controls="recent-activity-panel"
                            >
                                Toggle
                            </button>
                        </div>

                        <div id="recent-activity-panel" class="collapse show" aria-labelledby="recent-activity-title">
                            @if ($recentActivity->isEmpty())
                                <p class="af-muted mb-0">No activity yet. Start a survey from the list.</p>
                            @else
                                <ul class="af-list">
                                    @foreach ($recentActivity as $activity)
                                        <li class="af-list-item">
                                            <div>
                                                <strong>{{ $activity['title'] }}</strong>
                                                <div class="af-muted small">{{ $activity['status'] }}</div>
                                            </div>
                                            <div class="d-flex flex-column align-items-end gap-1">
                                                <span class="af-muted small">{{ $activity['time'] }}</span>
                                                @if (! empty($activity['slug']))
                                                    <a href="{{ route('surveys.public.show', $activity['slug']) }}" class="btn btn-sm btn-outline-secondary">Open</a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card af-card mt-4">
                    <div class="card-body">
                        <div class="af-card-header mb-3">
                            <h3 class="h5 af-section-title" id="preferences-title">Accessibility Preferences</h3>
                            <button
                                class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#preferences-panel"
                                aria-expanded="true"
                                aria-controls="preferences-panel"
                            >
                                Toggle
                            </button>
                        </div>

                        <div id="preferences-panel" class="collapse show" aria-labelledby="preferences-title">
                            @include('respondent.partials._a11y_prefs')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
