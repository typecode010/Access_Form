@php
    $isEdit = isset($survey) && $survey->exists;
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
        aria-describedby="title-help"
    >
    <div id="title-help" class="form-text">Use a short, clear title for respondents.</div>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        class="form-control @error('description') is-invalid @enderror"
        aria-describedby="description-help"
    >{{ old('description', $survey->description ?? '') }}</textarea>
    <div id="description-help" class="form-text">Optional instructions for respondents.</div>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="status" class="form-label">Status</label>
    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
        @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $survey->status ?? 'draft') === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Save Changes' : 'Create Survey' }}
    </button>

    <a href="{{ route('creator.surveys.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
