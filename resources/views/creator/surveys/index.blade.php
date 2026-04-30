@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Manage your surveys and start building questions in the next step.</p>
            </div>

            <a href="{{ route('creator.surveys.create') }}" class="btn btn-primary">Create Survey</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <caption class="visually-hidden">Creator surveys table</caption>
                <thead class="table-light">
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Status</th>
                        <th scope="col">Public Slug</th>
                        <th scope="col">Updated</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($surveys as $survey)
                        <tr>
                            <td>
                                <strong>{{ $survey->title }}</strong>
                            </td>
                            <td>
                                @php
                                    $badgeClass = $survey->status === 'published'
                                        ? 'text-bg-success'
                                        : ($survey->status === 'archived' ? 'text-bg-secondary' : 'text-bg-warning');
                                @endphp
                                <span class="badge {{ $badgeClass }} text-uppercase">{{ $survey->status }}</span>
                            </td>
                            <td><code>{{ $survey->public_slug }}</code></td>
                            <td>{{ $survey->updated_at?->diffForHumans() ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('creator.surveys.show', $survey) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-sm btn-outline-dark">Questions</a>
                                    <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-sm btn-outline-primary">Edit</a>

                                    <form method="POST" action="{{ route('creator.surveys.destroy', $survey) }}" onsubmit="return confirm('Delete this survey?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No surveys yet. Click "Create Survey" to start.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $surveys->links('pagination::bootstrap-5') }}
        </div>
    </section>
@endsection
