@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <h2 id="page-title" class="h3 mb-0">{{ $pageTitle }}</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-outline-primary">Edit Survey</a>
                <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-primary">Question Builder</a>
                <a href="{{ route('creator.surveys.index') }}" class="btn btn-outline-secondary">Back to Surveys</a>
            </div>
        </div>

        <dl class="row">
            <dt class="col-sm-3">Title</dt>
            <dd class="col-sm-9">{{ $survey->title }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                <span class="badge text-bg-info text-uppercase">{{ $survey->status }}</span>
            </dd>

            <dt class="col-sm-3">Public Slug</dt>
            <dd class="col-sm-9"><code>{{ $survey->public_slug }}</code></dd>

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9">{{ $survey->description ?: 'No description added.' }}</dd>

            <dt class="col-sm-3">Created</dt>
            <dd class="col-sm-9">{{ $survey->created_at?->toDayDateTimeString() }}</dd>

            <dt class="col-sm-3">Last Updated</dt>
            <dd class="col-sm-9">{{ $survey->updated_at?->toDayDateTimeString() }}</dd>
        </dl>
    </section>
@endsection
