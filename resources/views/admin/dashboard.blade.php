@extends('layouts.role-app')

@section('content')
    @php
        $formatCount = static fn ($value) => is_null($value) ? '—' : number_format($value);
        $severityLabels = [
            'error' => 'Errors',
            'warning' => 'Warnings',
            'info' => 'Info',
        ];
    @endphp

    <section class="admin-dashboard" aria-labelledby="admin-dashboard-title">
        @if (session('status'))
            <div class="alert alert-success mb-3" role="status" aria-live="polite">
                {{ session('status') }}
            </div>
        @endif

        <div class="admin-hero mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <p class="text-uppercase text-muted small mb-1">Admin Dashboard</p>
                    <h2 id="admin-dashboard-title" class="h3 mb-2">Welcome, {{ auth()->user()->name }}</h2>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge text-bg-primary admin-role-badge">Active role: {{ $roleName }}</span>
                        <span class="text-muted small">System overview and compliance snapshot.</span>
                    </div>
                </div>
                <nav class="admin-quick-links" aria-label="Admin quick links">
                    <a class="btn btn-primary btn-sm focus-ring" href="{{ route('admin.users.index') }}">Manage Users</a>
                    <a class="btn btn-outline-primary btn-sm focus-ring" href="{{ route('admin.accessibility.issues.index') }}">Accessibility Issues</a>
                    <a class="btn btn-outline-secondary btn-sm focus-ring" href="{{ route('profile.edit') }}">Profile</a>
                </nav>
            </div>
        </div>

        <section aria-labelledby="admin-kpi-title" class="mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                <h3 id="admin-kpi-title" class="h5 mb-0">System metrics</h3>
                <span class="text-muted small">Live counts from the database</span>
            </div>

            <div class="af-stat-grid">
                <div class="card stat-card stat-card-primary">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Users</p>
                        <p class="stat-value mb-1">{{ $formatCount($metrics['users']) }}</p>
                        <p class="small text-muted mb-0">Active roles across the platform.</p>
                    </div>
                </div>

                <div class="card stat-card stat-card-accent">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Surveys</p>
                        <p class="stat-value mb-1">{{ $formatCount($metrics['surveys']) }}</p>
                        <p class="small text-muted mb-0">Creator workspace inventory.</p>
                    </div>
                </div>

                <div class="card stat-card stat-card-success">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Published Surveys</p>
                        <p class="stat-value mb-1">{{ $formatCount($metrics['published_surveys']) }}</p>
                        <p class="small text-muted mb-0">Surveys currently visible to respondents.</p>
                    </div>
                </div>

                <div class="card stat-card stat-card-warning">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Responses</p>
                        <p class="stat-value mb-1">{{ $formatCount($metrics['responses']) }}</p>
                        @if (is_null($metrics['responses']))
                            <p class="small text-muted mb-0">Responses table not available.</p>
                        @else
                            <p class="small text-muted mb-0">Submissions recorded system-wide.</p>
                        @endif
                    </div>
                </div>

                <div class="card stat-card stat-card-danger">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Open Accessibility Issues</p>
                        <p class="stat-value mb-1">{{ $formatCount($metrics['open_issues']) }}</p>
                        @if (is_null($metrics['open_issues']))
                            <p class="small text-muted mb-0">Issues table not available.</p>
                        @else
                            <p class="small text-muted mb-0">Open items needing attention.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section aria-labelledby="admin-actions-title" class="mb-4">
            <div class="card admin-panel">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                        <div>
                            <h3 id="admin-actions-title" class="h5 mb-1">Quick actions</h3>
                            <p class="text-muted small mb-0">Shortcuts to keep operations moving.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-outline-primary btn-sm focus-ring" href="{{ route('admin.users.index') }}">Manage Users</a>
                            <a class="btn btn-outline-secondary btn-sm focus-ring" href="{{ route('admin.accessibility.issues.index') }}">View Accessibility Issues</a>
                            <a class="btn btn-outline-secondary btn-sm focus-ring" href="#admin-recent-activity">View Recent Activity</a>
                            <button
                                class="btn btn-outline-dark btn-sm focus-ring"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#admin-system-checklist"
                                aria-expanded="false"
                                aria-controls="admin-system-checklist"
                            >
                                System Checklist
                            </button>
                        </div>
                    </div>

                    <div class="collapse mt-3" id="admin-system-checklist">
                        <div class="admin-subpanel">
                            <h4 class="h6 mb-3">System checklist</h4>
                            <ul class="list-unstyled mb-0 admin-checklist">
                                <li class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                                    <span>Database migrations applied</span>
                                    <span class="badge bg-success-subtle text-success">OK</span>
                                </li>
                                <li class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                                    <span>Storage link present for media uploads</span>
                                    <span class="badge bg-success-subtle text-success">OK</span>
                                </li>
                                <li class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                                    <span>Accessibility checks running on survey updates</span>
                                    <span class="badge bg-warning-subtle text-warning">Review</span>
                                </li>
                                <li class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                                    <span>Audit logging coverage</span>
                                    <span class="badge bg-warning-subtle text-warning">Review</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <section class="col-lg-6" aria-labelledby="admin-a11y-title">
                <div class="card admin-panel h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <h3 id="admin-a11y-title" class="h5 mb-1">Accessibility compliance snapshot</h3>
                                <p class="text-muted small mb-0">Open issues by severity and top issue types.</p>
                            </div>
                            <a class="btn btn-sm btn-outline-primary focus-ring" href="{{ route('admin.accessibility.issues.index') }}">Review issues</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (! $tables['accessibility_issues'])
                            <div class="admin-empty-state" role="status">
                                <p class="mb-1">Accessibility issues table not available.</p>
                                <p class="small text-muted mb-0">Run migrations to enable live compliance data.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <h4 class="h6">Open issues by severity</h4>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($severityLabels as $key => $label)
                                            <li class="d-flex justify-content-between align-items-center mb-2">
                                                <span>{{ $label }}</span>
                                                <span class="badge badge-severity-{{ $key }}">{{ $severityCounts[$key] ?? 0 }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-7">
                                    <h4 class="h6">Top issue types</h4>
                                    @forelse ($topIssueTypes as $issue)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $issue->issue_type)) }}</span>
                                            <span class="badge text-bg-light border">{{ $issue->total }}</span>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No open issues yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="col-lg-6" aria-labelledby="admin-recent-activity-title" id="admin-recent-activity">
                <div class="card admin-panel h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2">
                            <div>
                                <h3 id="admin-recent-activity-title" class="h5 mb-1">Recent activity</h3>
                                <p class="text-muted small mb-0">Latest actions across the platform.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label for="admin-activity-filter" class="small text-muted">Filter</label>
                                <select
                                    id="admin-activity-filter"
                                    class="form-select form-select-sm"
                                    data-admin-activity-filter
                                    aria-describedby="admin-activity-status"
                                >
                                    <option value="all">All</option>
                                    <option value="surveys">Surveys</option>
                                    <option value="users">Users</option>
                                    <option value="issues">Issues</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="small text-muted mb-2" id="admin-activity-status" data-admin-activity-status aria-live="polite"></div>

                        @if (! $tables['audit_logs'])
                            <div class="admin-empty-state" role="status">
                                <p class="mb-1">Audit logs table not available.</p>
                                <p class="small text-muted mb-0">TODO: Run migrations to enable activity tracking.</p>
                            </div>
                        @elseif ($recentAuditLogs->isEmpty())
                            <div class="admin-empty-state" role="status">
                                <p class="mb-1">No recent activity yet.</p>
                                <p class="small text-muted mb-0">Once users take action, events will appear here.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle af-table">
                                    <caption class="text-muted">Latest 10 actions recorded in the system.</caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">Action</th>
                                            <th scope="col">Entity</th>
                                            <th scope="col">Actor</th>
                                            <th scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentAuditLogs as $log)
                                            @php
                                                $entityType = $log->entity_type;
                                                $category = 'other';
                                                if (\Illuminate\Support\Str::contains($entityType, 'Survey')) {
                                                    $category = 'surveys';
                                                } elseif (\Illuminate\Support\Str::contains($entityType, 'User')) {
                                                    $category = 'users';
                                                } elseif (\Illuminate\Support\Str::contains($entityType, 'AccessibilityIssue')) {
                                                    $category = 'issues';
                                                }
                                            @endphp
                                            <tr data-admin-activity-item data-admin-activity-type="{{ $category }}">
                                                <td>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $log->action)) }}</td>
                                                <td>{{ \Illuminate\Support\Str::headline(class_basename($log->entity_type)) }}</td>
                                                <td>
                                                    <div>{{ $log->actor?->name ?? 'System' }}</div>
                                                    @if ($log->actor?->email)
                                                        <div class="text-muted small">{{ $log->actor->email }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <time datetime="{{ $log->created_at?->toISOString() }}">
                                                        {{ $log->created_at?->diffForHumans() }}
                                                    </time>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-muted small d-none" data-admin-activity-empty>No activity matches this filter.</div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
