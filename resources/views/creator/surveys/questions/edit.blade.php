@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-outline-secondary">Back to Questions</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="polite">
                Please review the highlighted fields and try again.
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h3 class="h5 mb-3">Question Details</h3>

                <form method="POST" action="{{ route('creator.surveys.questions.update', [$survey, $question]) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('creator.surveys.questions._form', [
                        'survey' => $survey,
                        'question' => $question,
                        'questionTypes' => $questionTypes,
                        'submitLabel' => 'Save Question',
                    ])
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="h5 mb-3">Option Management</h3>

                @if (! $question->requiresOptions())
                    <div class="alert alert-info mb-0" role="status">
                        This question type does not use options. Switch to "Multiple Choice" to manage options.
                    </div>
                @else
                    <p class="text-muted">For multiple choice questions, keep at least 2 options.</p>

                    <form method="POST" action="{{ route('creator.surveys.questions.options.store', [$survey, $question]) }}" class="row g-3 mb-4" novalidate>
                        @csrf

                        <div class="col-md-5">
                            <label for="option_text" class="form-label">Option Text</label>
                            <input
                                id="option_text"
                                type="text"
                                name="option_text"
                                class="form-control @error('option_text') is-invalid @enderror"
                                value="{{ old('option_text') }}"
                                required
                                aria-describedby="{{ trim('option-text-help '.($errors->has('option_text') ? 'option-text-error' : '')) }}"
                                @error('option_text') aria-invalid="true" @enderror
                            >
                            <div id="option-text-help" class="form-text">Visible label for respondents.</div>
                            @error('option_text')
                                <div id="option-text-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="option_value" class="form-label">Option Value (optional)</label>
                            <input
                                id="option_value"
                                type="text"
                                name="option_value"
                                class="form-control @error('option_value') is-invalid @enderror"
                                value="{{ old('option_value') }}"
                                @error('option_value') aria-invalid="true" @enderror
                            >
                            @error('option_value')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="option_position" class="form-label">Position</label>
                            <input
                                id="option_position"
                                type="number"
                                name="position"
                                min="1"
                                class="form-control @error('position') is-invalid @enderror"
                                value="{{ old('position') }}"
                                @error('position') aria-invalid="true" @enderror
                            >
                            @error('position')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Add Option</button>
                        </div>
                    </form>

                    <div class="vstack gap-3">
                        @forelse ($question->options as $option)
                            <article class="border rounded p-3" aria-labelledby="option-heading-{{ $option->id }}">
                                <h4 id="option-heading-{{ $option->id }}" class="h6 mb-3">Option #{{ $option->position }}</h4>

                                <form method="POST" action="{{ route('creator.surveys.questions.options.update', [$survey, $question, $option]) }}" class="row g-3 align-items-end" novalidate>
                                    @csrf
                                    @method('PUT')

                                    <div class="col-md-4">
                                        <label for="option_text_{{ $option->id }}" class="form-label">Option Text</label>
                                        <input
                                            id="option_text_{{ $option->id }}"
                                            type="text"
                                            name="option_text"
                                            class="form-control"
                                            value="{{ old('option_text', $option->option_text) }}"
                                            required
                                        >
                                    </div>

                                    <div class="col-md-4">
                                        <label for="option_value_{{ $option->id }}" class="form-label">Option Value</label>
                                        <input
                                            id="option_value_{{ $option->id }}"
                                            type="text"
                                            name="option_value"
                                            class="form-control"
                                            value="{{ old('option_value', $option->option_value) }}"
                                        >
                                    </div>

                                    <div class="col-md-2">
                                        <label for="option_position_{{ $option->id }}" class="form-label">Position</label>
                                        <input
                                            id="option_position_{{ $option->id }}"
                                            type="number"
                                            name="position"
                                            min="1"
                                            class="form-control"
                                            value="{{ old('position', $option->position) }}"
                                        >
                                    </div>

                                    <div class="col-md-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-outline-primary w-100">Update</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('creator.surveys.questions.options.destroy', [$survey, $question, $option]) }}" class="mt-3" onsubmit="return confirm('Delete this option?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete Option</button>
                                </form>
                            </article>
                        @empty
                            <p class="mb-0 text-muted">No options yet. Add at least 2 options for this multiple choice question.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
