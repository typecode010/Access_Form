@php
    $settingsValue = old('settings_json');

    if ($settingsValue === null && ! empty($question->settings_json)) {
        $settingsValue = json_encode($question->settings_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
@endphp

<div class="mb-3">
    <label for="type" class="form-label">Question Type</label>
    <select
        id="type"
        name="type"
        class="form-select @error('type') is-invalid @enderror"
        required
        aria-describedby="type-help"
    >
        @foreach ($questionTypes as $value => $label)
            <option value="{{ $value }}" @selected(old('type', $question->type) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <div id="type-help" class="form-text">Select how respondents will answer this question.</div>
    @error('type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="prompt" class="form-label">Prompt</label>
    <textarea
        id="prompt"
        name="prompt"
        rows="3"
        class="form-control @error('prompt') is-invalid @enderror"
        required
        aria-describedby="prompt-help"
    >{{ old('prompt', $question->prompt) }}</textarea>
    <div id="prompt-help" class="form-text">Write a clear and concise question prompt.</div>
    @error('prompt')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="help_text" class="form-label">Help Text</label>
    <textarea
        id="help_text"
        name="help_text"
        rows="2"
        class="form-control @error('help_text') is-invalid @enderror"
        aria-describedby="help-text-help"
    >{{ old('help_text', $question->help_text) }}</textarea>
    <div id="help-text-help" class="form-text">Optional guidance shown to respondents.</div>
    @error('help_text')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-6">
        <label for="position" class="form-label">Position</label>
        <input
            id="position"
            type="number"
            name="position"
            min="1"
            value="{{ old('position', $question->position) }}"
            class="form-control @error('position') is-invalid @enderror"
            aria-describedby="position-help"
        >
        <div id="position-help" class="form-text">Leave blank to place this question at the end.</div>
        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-sm-6">
        <label for="settings_json" class="form-label">Settings JSON</label>
        <textarea
            id="settings_json"
            name="settings_json"
            rows="3"
            class="form-control @error('settings_json') is-invalid @enderror"
            aria-describedby="settings-help"
        >{{ $settingsValue }}</textarea>
        <div id="settings-help" class="form-text">Optional JSON for advanced settings such as rating min/max.</div>
        @error('settings_json')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-check mb-4">
    <input type="hidden" name="is_required" value="0">
    <input
        id="is_required"
        type="checkbox"
        name="is_required"
        value="1"
        class="form-check-input"
        @checked((bool) old('is_required', $question->is_required))
    >
    <label class="form-check-label" for="is_required">Required question</label>
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-outline-secondary">Back to Questions</a>
</div>
