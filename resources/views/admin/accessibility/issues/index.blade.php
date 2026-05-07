@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Review detected accessibility risks across published and draft surveys.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back to Admin</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.accessibility.issues.index') }}" class="af-filter-bar" aria-describedby="filter-help">
            <p id="filter-help" class="text-muted small mb-3">Use the filters to find specific issues.</p>
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="filter-status" class="form-label">Status</label>
                    <select id="filter-status" name="status" class="form-select">
                        <option value="">All</option>
                        <option value="open" @selected($filters['status'] === 'open')>Open</option>
                        <option value="resolved" @selected($filters['status'] === 'resolved')>Resolved</option>
                        <option value="ignored" @selected($filters['status'] === 'ignored')>Ignored</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter-severity" class="form-label">Severity</label>
                    <select id="filter-severity" name="severity" class="form-select">
                        <option value="">All</option>
                        <option value="error" @selected($filters['severity'] === 'error')>Error</option>
                        <option value="warning" @selected($filters['severity'] === 'warning')>Warning</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-type" class="form-label">Issue Type</label>
                    <select id="filter-type" name="issue_type" class="form-select">
                        <option value="">All</option>
                        @foreach ($issueTypes as $issueType)
                            <option value="{{ $issueType }}" @selected($filters['issue_type'] === $issueType)>
                                {{ $issueType }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter-survey" class="form-label">Survey ID</label>
                    <input id="filter-survey" type="number" name="survey_id" class="form-control" value="{{ $filters['survey_id'] }}">
                </div>
                <div class="col-md-2">
                    <label for="filter-creator" class="form-label">Creator ID</label>
                    <input id="filter-creator" type="number" name="creator_id" class="form-control" value="{{ $filters['creator_id'] }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.accessibility.issues.index') }}" class="btn btn-outline-secondary btn-sm">Clear filters</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle af-table">
                <caption class="visually-hidden">Accessibility issue registry</caption>
                <thead class="table-light">
                    <tr>
                        <th scope="col">Detected</th>
                        <th scope="col">Severity</th>
                        <th scope="col">Issue Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Survey</th>
                        <th scope="col">Creator</th>
                        <th scope="col">Message</th>
                        <th scope="col">Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($issues as $issue)
                        @php
                            $survey = $issue->survey;
                            $creator = $survey?->creator;
                            $severityClass = $issue->severity === 'error' ? 'text-bg-danger' : 'text-bg-warning';
                            $statusClass = $issue->status === 'resolved'
                                ? 'text-bg-success'
                                : ($issue->status === 'ignored' ? 'text-bg-secondary' : 'text-bg-primary');
                        @endphp
                        <tr>
                            <td>{{ $issue->detected_at?->toDayDateTimeString() ?? $issue->created_at?->toDayDateTimeString() }}</td>
                            <td><span class="badge {{ $severityClass }} text-uppercase">{{ $issue->severity }}</span></td>
                            <td>{{ $issue->issue_type }}</td>
                            <td><span class="badge {{ $statusClass }} text-uppercase">{{ $issue->status }}</span></td>
                            <td>
                                @if ($survey)
                                    <div class="fw-semibold">{{ $survey->title }}</div>
                                    <div class="text-muted small">ID: {{ $survey->id }}</div>
                                @else
                                    <span class="text-muted">Survey removed</span>
                                @endif
                            </td>
                            <td>
                                @if ($creator)
                                    <div class="fw-semibold">{{ $creator->name }}</div>
                                    <div class="text-muted small">ID: {{ $creator->id }}</div>
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>{{ $issue->message }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.accessibility.issues.update', $issue) }}" class="d-flex flex-column gap-2" aria-label="Update issue status">
                                    @csrf
                                    @method('PUT')
                                    <label class="visually-hidden" for="issue-status-{{ $issue->id }}">Status</label>
                                    <select id="issue-status-{{ $issue->id }}" name="status" class="form-select form-select-sm">
                                        <option value="open" @selected($issue->status === 'open')>Open</option>
                                        <option value="resolved" @selected($issue->status === 'resolved')>Resolved</option>
                                        <option value="ignored" @selected($issue->status === 'ignored')>Ignored</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No issues found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $issues->links('pagination::bootstrap-5') }}
        </div>
    </section>
@endsection
