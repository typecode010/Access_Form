@extends('layouts.role-app')

@section('content')
    @php
        $contrastErrorId = $errors->has('high_contrast_enabled') ? 'contrast-error' : null;
        $dyslexiaErrorId = $errors->has('dyslexia_friendly_enabled') ? 'dyslexia-error' : null;
        $keyboardErrorId = $errors->has('keyboard_nav_enforced') ? 'keyboard-error' : null;
        $textSizeErrorId = $errors->has('text_size') ? 'text-size-error' : null;
        $motionErrorId = $errors->has('reduced_motion') ? 'motion-error' : null;
    @endphp

    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('creator.surveys.preview', $survey) }}" class="btn btn-outline-success">Preview</a>
                <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-outline-primary">Edit Survey</a>
                <a href="{{ route('creator.surveys.index') }}" class="btn btn-outline-secondary">Back to Surveys</a>
            </div>
        </div>

        <p class="text-muted">Configure accessibility options that will be applied to the survey preview and respondent experience.</p>

        @if (session('status'))
            <div class="alert alert-success" role="status" aria-live="polite">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="polite">
                Please review the highlighted fields and try again.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('creator.surveys.accessibility.update', $survey) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <fieldset class="mb-4">
                        <legend class="h5">Display and navigation</legend>

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="high_contrast_enabled" value="0">
                            <input
                                class="form-check-input @error('high_contrast_enabled') is-invalid @enderror"
                                type="checkbox"
                                id="high_contrast_enabled"
                                name="high_contrast_enabled"
                                value="1"
                                @checked(old('high_contrast_enabled', $settings->high_contrast_enabled))
                                aria-describedby="{{ trim('contrast-help '.($contrastErrorId ?? '')) }}"
                                @error('high_contrast_enabled') aria-invalid="true" @enderror
                            >
                            <label class="form-check-label" for="high_contrast_enabled">High contrast mode</label>
                            <div id="contrast-help" class="form-text">Improves readability with high-contrast colors.</div>
                            @error('high_contrast_enabled')
                                <div id="contrast-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="dyslexia_friendly_enabled" value="0">
                            <input
                                class="form-check-input @error('dyslexia_friendly_enabled') is-invalid @enderror"
                                type="checkbox"
                                id="dyslexia_friendly_enabled"
                                name="dyslexia_friendly_enabled"
                                value="1"
                                @checked(old('dyslexia_friendly_enabled', $settings->dyslexia_friendly_enabled))
                                aria-describedby="{{ trim('dyslexia-help '.($dyslexiaErrorId ?? '')) }}"
                                @error('dyslexia_friendly_enabled') aria-invalid="true" @enderror
                            >
                            <label class="form-check-label" for="dyslexia_friendly_enabled">Dyslexia-friendly typography</label>
                            <div id="dyslexia-help" class="form-text">Adds spacing and line-height improvements.</div>
                            @error('dyslexia_friendly_enabled')
                                <div id="dyslexia-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="keyboard_nav_enforced" value="0">
                            <input
                                class="form-check-input @error('keyboard_nav_enforced') is-invalid @enderror"
                                type="checkbox"
                                id="keyboard_nav_enforced"
                                name="keyboard_nav_enforced"
                                value="1"
                                @checked(old('keyboard_nav_enforced', $settings->keyboard_nav_enforced))
                                aria-describedby="{{ trim('keyboard-help '.($keyboardErrorId ?? '')) }}"
                                @error('keyboard_nav_enforced') aria-invalid="true" @enderror
                            >
                            <label class="form-check-label" for="keyboard_nav_enforced">Enforce keyboard-only navigation</label>
                            <div id="keyboard-help" class="form-text">Recommended for accessibility-first compliance.</div>
                            @error('keyboard_nav_enforced')
                                <div id="keyboard-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </fieldset>

                    <fieldset class="mb-4">
                        <legend class="h5">Comfort settings</legend>

                        <div class="mb-3">
                            <label for="text_size" class="form-label">Text size</label>
                            <select
                                id="text_size"
                                name="text_size"
                                class="form-select @error('text_size') is-invalid @enderror"
                                aria-describedby="{{ trim('text-size-help '.($textSizeErrorId ?? '')) }}"
                                @error('text_size') aria-invalid="true" @enderror
                            >
                                <option value="">Default (md)</option>
                                @foreach (['sm' => 'Small', 'md' => 'Medium', 'lg' => 'Large'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('text_size', $settings->text_size) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="text-size-help" class="form-text">Adjust the base font size for respondents.</div>
                            @error('text_size')
                                <div id="text-size-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="reduced_motion" value="0">
                            <input
                                class="form-check-input @error('reduced_motion') is-invalid @enderror"
                                type="checkbox"
                                id="reduced_motion"
                                name="reduced_motion"
                                value="1"
                                @checked(old('reduced_motion', $settings->reduced_motion))
                                aria-describedby="{{ trim('motion-help '.($motionErrorId ?? '')) }}"
                                @error('reduced_motion') aria-invalid="true" @enderror
                            >
                            <label class="form-check-label" for="reduced_motion">Reduce motion</label>
                            <div id="motion-help" class="form-text">Minimizes animations for motion-sensitive users.</div>
                            @error('reduced_motion')
                                <div id="motion-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </fieldset>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <a href="{{ route('creator.surveys.preview', $survey) }}" class="btn btn-outline-secondary">Preview</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
