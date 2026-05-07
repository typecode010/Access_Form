@extends('layouts.role-app')

@section('content')
    @php
        $themeClasses = [];

        if ($settings?->high_contrast_enabled) {
            $themeClasses[] = 'theme-contrast';
        }

        if ($settings?->dyslexia_friendly_enabled) {
            $themeClasses[] = 'theme-dyslexia';
        }

        if ($settings?->reduced_motion) {
            $themeClasses[] = 'reduced-motion';
        }

        if (! empty($settings?->text_size)) {
            $themeClasses[] = 'text-size-'.$settings->text_size;
        }

        $themeClassString = trim('preview-theme '.implode(' ', $themeClasses));
    @endphp

    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('creator.surveys.accessibility.edit', $survey) }}" class="btn btn-outline-success">Accessibility Settings</a>
                <a href="{{ route('creator.surveys.media.index', $survey) }}" class="btn btn-outline-primary">Media</a>
                <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-outline-primary">Edit Survey</a>
                <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-primary">Question Builder</a>
                <a href="{{ route('creator.surveys.index') }}" class="btn btn-outline-secondary">Back to Surveys</a>
            </div>
        </div>

        <div class="alert alert-info" role="status">
            Preview only. This page does not submit responses.
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card {{ $themeClassString }}">
                    <div class="card-body">
                        @include('shared._survey_media', ['mediaItems' => $survey->media, 'context' => 'preview'])
                        <form novalidate aria-describedby="preview-note">
                            <p id="preview-note" class="text-muted">Use this view to review question flow and accessibility checks.</p>

                            @forelse ($questions as $question)
                                @php
                                    $helpId = $question->help_text ? 'question-help-'.$question->id : null;
                                    $settings = is_array($question->settings_json) ? $question->settings_json : [];
                                    $ratingMin = isset($settings['min']) ? (int) $settings['min'] : 1;
                                    $ratingMax = isset($settings['max']) ? (int) $settings['max'] : 5;
                                    $ratingMin = max(1, $ratingMin);
                                    $ratingMax = max($ratingMin, $ratingMax);
                                @endphp

                                <fieldset class="mb-4" @if ($helpId) aria-describedby="{{ $helpId }}" @endif>
                                    <legend class="h6 mb-2">
                                        {{ $question->position }}. {{ $question->prompt }}
                                        @if ($question->is_required)
                                            <span class="text-danger" aria-label="Required">*</span>
                                        @endif
                                    </legend>

                                    @if ($question->help_text)
                                        <div id="{{ $helpId }}" class="text-muted small mb-2">{{ $question->help_text }}</div>
                                    @endif

                                    @if ($question->type === 'multiple_choice')
                                        @if ($question->options->isEmpty())
                                            <p class="text-muted">No options added yet.</p>
                                        @else
                                            <div class="vstack gap-2">
                                                @foreach ($question->options as $option)
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="question_{{ $question->id }}"
                                                            id="option_{{ $option->id }}"
                                                            value="{{ $option->option_value ?? $option->option_text }}"
                                                        >
                                                        <label class="form-check-label" for="option_{{ $option->id }}">
                                                            {{ $option->option_text }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @elseif ($question->type === 'text')
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="question_{{ $question->id }}"
                                            placeholder="Type your answer"
                                        >
                                    @elseif ($question->type === 'rating')
                                        <div class="d-flex flex-wrap gap-3">
                                            @for ($i = $ratingMin; $i <= $ratingMax; $i++)
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="question_{{ $question->id }}"
                                                        id="rating_{{ $question->id }}_{{ $i }}"
                                                        value="{{ $i }}"
                                                    >
                                                    <label class="form-check-label" for="rating_{{ $question->id }}_{{ $i }}">{{ $i }}</label>
                                                </div>
                                            @endfor
                                        </div>
                                    @elseif ($question->type === 'file')
                                        @php
                                            $acceptTypes = isset($settings['allowed_types']) && is_array($settings['allowed_types'])
                                                ? implode(',', $settings['allowed_types'])
                                                : null;
                                        @endphp
                                        <input
                                            type="file"
                                            class="form-control"
                                            name="question_{{ $question->id }}"
                                            @if ($acceptTypes)
                                                accept="{{ $acceptTypes }}"
                                            @endif
                                        >
                                        <p class="text-muted small mt-2">Upload constraints can be configured in Settings JSON.</p>
                                    @endif
                                </fieldset>
                            @empty
                                <p class="text-muted mb-0">No questions added yet.</p>
                            @endforelse

                            <button type="button" class="btn btn-secondary" disabled aria-disabled="true">
                                Submit (disabled in preview)
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h5">Accessibility checklist</h3>

                        @if ($checklist['hasIssues'])
                            <p class="text-muted">Review these items before publishing.</p>
                            <ul class="mb-0">
                                @foreach ($checklist['issues'] as $issue)
                                    <li>{{ $issue }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-success">No basic issues found. Continue with full accessibility review.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
