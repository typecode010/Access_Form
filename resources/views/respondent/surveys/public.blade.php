@extends('layouts.public')

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

        $themeClassString = trim('public-survey '.implode(' ', $themeClasses));
    @endphp

    <section class="{{ $themeClassString }}" aria-labelledby="survey-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 id="survey-title" class="h3 mb-1">{{ $survey->title }}</h2>
                <p class="af-muted mb-0">{{ $survey->description ?: 'Please complete the survey below.' }}</p>
            </div>
            <div class="text-muted">Estimated time: {{ max(2, (int) ceil($questions->count() / 2)) }} min</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="polite">
                Please review the highlighted fields and try again.
            </div>
        @endif

        @include('shared._survey_media', ['mediaItems' => $survey->media, 'context' => 'public'])

        <form method="POST" action="{{ route('surveys.public.submit', $survey->public_slug) }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="vstack gap-4">
                @forelse ($questions as $question)
                    @php
                        $helpId = $question->help_text ? 'question-help-'.$question->id : null;
                        $errorId = 'question-error-'.$question->id;
                        $settingsJson = is_array($question->settings_json) ? $question->settings_json : [];
                        $ratingMin = isset($settingsJson['min']) ? (int) $settingsJson['min'] : 1;
                        $ratingMax = isset($settingsJson['max']) ? (int) $settingsJson['max'] : 5;
                        $ratingMin = max(1, $ratingMin);
                        $ratingMax = max($ratingMin, $ratingMax);
                        $fieldId = 'question-'.$question->id;
                        $describedBy = trim(($helpId ? $helpId.' ' : '').$errorId);
                    @endphp

                    <fieldset class="border rounded p-3" aria-describedby="{{ $describedBy }}">
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
                                <p class="text-muted">Options not configured yet.</p>
                            @else
                                <div class="vstack gap-2">
                                    @foreach ($question->options as $option)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="answers[{{ $question->id }}]"
                                                id="{{ $fieldId }}-{{ $option->id }}"
                                                value="{{ $option->id }}"
                                                @checked(old('answers.'.$question->id) == $option->id)
                                                @if ($question->is_required) required @endif
                                            >
                                            <label class="form-check-label" for="{{ $fieldId }}-{{ $option->id }}">
                                                {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif ($question->type === 'text')
                            <textarea
                                id="{{ $fieldId }}"
                                name="answers[{{ $question->id }}]"
                                rows="3"
                                class="form-control @error('answers.'.$question->id) is-invalid @enderror"
                                @if ($question->is_required) required @endif
                            >{{ old('answers.'.$question->id) }}</textarea>
                        @elseif ($question->type === 'rating')
                            <div class="d-flex flex-wrap gap-3">
                                @for ($i = $ratingMin; $i <= $ratingMax; $i++)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            id="{{ $fieldId }}-{{ $i }}"
                                            value="{{ $i }}"
                                            @checked(old('answers.'.$question->id) == $i)
                                            @if ($question->is_required) required @endif
                                        >
                                        <label class="form-check-label" for="{{ $fieldId }}-{{ $i }}">{{ $i }}</label>
                                    </div>
                                @endfor
                            </div>
                        @elseif ($question->type === 'file')
                            @php
                                $acceptTypes = ! empty($settingsJson['allowed_types']) && is_array($settingsJson['allowed_types'])
                                    ? implode(',', $settingsJson['allowed_types'])
                                    : null;
                            @endphp
                            <input
                                id="{{ $fieldId }}"
                                type="file"
                                name="files[{{ $question->id }}]"
                                class="form-control @error('files.'.$question->id) is-invalid @enderror"
                                @if ($acceptTypes) accept="{{ $acceptTypes }}" @endif
                                @if ($question->is_required) required @endif
                            >
                            <div class="form-text">
                                @if (! empty($settingsJson['max_size_kb']))
                                    Max size: {{ (int) $settingsJson['max_size_kb'] }} KB.
                                @else
                                    Upload a file that supports your answer.
                                @endif
                            </div>
                        @endif

                        @error('answers.'.$question->id)
                            <div id="{{ $errorId }}" class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                        @enderror
                        @error('files.'.$question->id)
                            <div id="{{ $errorId }}" class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                        @enderror
                    </fieldset>
                @empty
                    <p class="text-muted">No questions configured yet.</p>
                @endforelse
            </div>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Submit Survey</button>
                <a href="{{ route('respondent.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </form>
    </section>
@endsection
