@php
    $isEdit = isset($survey) && $survey->exists;
    $titleErrorId = $errors->has('title') ? 'title-error' : null;
    $descriptionErrorId = $errors->has('description') ? 'description-error' : null;
    $statusErrorId = $errors->has('status') ? 'status-error' : null;
@endphp

<div class="mb-3">
    <label for="title" class="form-label">Survey Title</label>
    <input
        id="title"
        name="title"
        type="text"
        class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $survey->title ?? '') }}"
        required
        maxlength="255"
        aria-describedby="{{ trim('title-help '.($titleErrorId ?? '')) }}"
        @error('title') aria-invalid="true" @enderror
    >
    <div id="title-help" class="form-text">Use a short, clear title for respondents.</div>
    @error('title')
        <div id="title-error" class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        class="form-control @error('description') is-invalid @enderror"
        aria-describedby="{{ trim('description-help '.($descriptionErrorId ?? '')) }}"
        @error('description') aria-invalid="true" @enderror
    >{{ old('description', $survey->description ?? '') }}</textarea>
    <div id="description-help" class="form-text">Optional instructions for respondents.</div>
    @error('description')
        <div id="description-error" class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="status" class="form-label">Status</label>
    <select
        id="status"
        name="status"
        class="form-select @error('status') is-invalid @enderror"
        required
        @if ($statusErrorId) aria-describedby="{{ $statusErrorId }}" @endif
        @error('status') aria-invalid="true" @enderror
    >
        @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $survey->status ?? 'draft') === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('status')
        <div id="status-error" class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Save Changes' : 'Create Survey' }}
    </button>

    <a href="{{ route('creator.surveys.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
