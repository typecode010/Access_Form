@extends('layouts.role-app')

@section('content')
    <section class="af-dashboard respondent-dashboard respondent-theme" data-respondent-theme aria-labelledby="page-title">
        <div class="af-page-intro">
            <h2 id="page-title" class="h3 mb-0">{{ $pageTitle }}</h2>
            <p class="af-muted mb-0">Browse published surveys and start a response.</p>
        </div>

        <div class="card af-card respondent-highlight">
            <div class="card-body">
                <div class="af-card-header mb-3">
                    <h3 class="h5 af-section-title" id="catalog-actions-title">Quick access</h3>
                    <button
                        class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#catalog-actions-panel"
                        aria-expanded="true"
                        aria-controls="catalog-actions-panel"
                    >
                        Toggle
                    </button>
                </div>

                <div id="catalog-actions-panel" class="collapse show" aria-labelledby="catalog-actions-title">
                    <form class="row g-3" data-slug-form>
                        <div class="col-md-7">
                            <label for="catalog-slug" class="form-label">Enter survey link or slug</label>
                            <input
                                id="catalog-slug"
                                type="text"
                                class="form-control"
                                placeholder="e.g. accessibility-demo-survey"
                                data-slug-input
                                aria-describedby="catalog-slug-help catalog-slug-status"
                            >
                            <div id="catalog-slug-help" class="form-text">You will be redirected to the survey page.</div>
                            <div id="catalog-slug-status" class="visually-hidden" role="status" aria-live="polite" data-slug-status></div>
                        </div>
                        <div class="col-md-5 d-flex align-items-end gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Go</button>
                            <a href="{{ route('respondent.submissions.index') }}" class="btn btn-outline-secondary">My submissions</a>
                            <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-dark">Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card af-card mt-4">
            <div class="card-body">
                <div class="af-card-header mb-3">
                    <div>
                        <h3 class="h5 af-section-title" id="available-surveys-title">Available Surveys</h3>
                        <p class="af-muted mb-0">Filtered list of published surveys.</p>
                    </div>
                    <button
                        class="btn btn-sm btn-outline-secondary af-collapse-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#catalog-surveys-panel"
                        aria-expanded="true"
                        aria-controls="catalog-surveys-panel"
                    >
                        Toggle
                    </button>
                </div>

                <div id="catalog-surveys-panel" class="collapse show" aria-labelledby="available-surveys-title">
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
    </section>
@endsection
