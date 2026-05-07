@extends('layouts.role-app')

@section('content')
    @php
        $typeErrorId = $errors->has('media_type') ? 'media-type-error' : null;
        $fileErrorId = $errors->has('media_file') ? 'media-file-error' : null;
        $altErrorId = $errors->has('alt_text') ? 'alt-text-error' : null;
        $captionErrorId = $errors->has('caption_file') ? 'caption-file-error' : null;
        $transcriptErrorId = $errors->has('transcript_text') ? 'transcript-text-error' : null;
        $signErrorId = $errors->has('sign_language_video_url') ? 'sign-language-error' : null;
        $positionErrorId = $errors->has('position') ? 'media-position-error' : null;
    @endphp

    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('creator.surveys.media.index', $survey) }}" class="btn btn-outline-secondary">Back to Media</a>
                <a href="{{ route('creator.surveys.preview', $survey) }}" class="btn btn-outline-success">Preview Survey</a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="polite">
                Please review the highlighted fields and try again.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('creator.surveys.media.update', [$survey, $media]) }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="media_type" class="form-label">Media Type</label>
                            <select
                                id="media_type"
                                name="media_type"
                                class="form-select @error('media_type') is-invalid @enderror"
                                aria-describedby="{{ trim('media-type-help '.($typeErrorId ?? '')) }}"
                                required
                                @error('media_type') aria-invalid="true" @enderror
                            >
                                <option value="image" @selected(old('media_type', $media->media_type) === 'image')>Image</option>
                                <option value="video" @selected(old('media_type', $media->media_type) === 'video')>Video</option>
                                <option value="audio" @selected(old('media_type', $media->media_type) === 'audio')>Audio</option>
                                <option value="other" @selected(old('media_type', $media->media_type) === 'other')>Other</option>
                            </select>
                            <div id="media-type-help" class="form-text">Update the primary media format.</div>
                            @error('media_type')
                                <div id="media-type-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="media_file" class="form-label">Replace File (optional)</label>
                            <input
                                id="media_file"
                                type="file"
                                name="media_file"
                                class="form-control @error('media_file') is-invalid @enderror"
                                aria-describedby="{{ trim('media-file-help '.($fileErrorId ?? '')) }}"
                                @error('media_file') aria-invalid="true" @enderror
                            >
                            <div id="media-file-help" class="form-text">
                                Current file: {{ $media->file_path ? basename($media->file_path) : 'None' }}
                            </div>
                            @error('media_file')
                                <div id="media-file-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="alt_text" class="form-label">Alt Text</label>
                            <input
                                id="alt_text"
                                type="text"
                                name="alt_text"
                                class="form-control @error('alt_text') is-invalid @enderror"
                                value="{{ old('alt_text', $media->alt_text) }}"
                                aria-describedby="{{ trim('alt-text-help '.($altErrorId ?? '')) }}"
                                @error('alt_text') aria-invalid="true" @enderror
                            >
                            <div id="alt-text-help" class="form-text">Required for images before publishing.</div>
                            @error('alt_text')
                                <div id="alt-text-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="caption_file" class="form-label">Replace Caption File (optional)</label>
                            <input
                                id="caption_file"
                                type="file"
                                name="caption_file"
                                class="form-control @error('caption_file') is-invalid @enderror"
                                aria-describedby="{{ trim('caption-file-help '.($captionErrorId ?? '')) }}"
                                @error('caption_file') aria-invalid="true" @enderror
                            >
                            <div id="caption-file-help" class="form-text">
                                @if ($media->caption_path)
                                    Current caption: {{ basename($media->caption_path) }}
                                @else
                                    No caption file uploaded.
                                @endif
                            </div>
                            @error('caption_file')
                                <div id="caption-file-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="transcript_text" class="form-label">Transcript (optional)</label>
                            <textarea
                                id="transcript_text"
                                name="transcript_text"
                                rows="4"
                                class="form-control @error('transcript_text') is-invalid @enderror"
                                aria-describedby="{{ trim('transcript-help '.($transcriptErrorId ?? '')) }}"
                                @error('transcript_text') aria-invalid="true" @enderror
                            >{{ old('transcript_text', $media->transcript_text) }}</textarea>
                            <div id="transcript-help" class="form-text">Provide a text transcript for audio or video content.</div>
                            @error('transcript_text')
                                <div id="transcript-text-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sign_language_video_url" class="form-label">Sign-language Video URL (optional)</label>
                            <input
                                id="sign_language_video_url"
                                type="url"
                                name="sign_language_video_url"
                                class="form-control @error('sign_language_video_url') is-invalid @enderror"
                                value="{{ old('sign_language_video_url', $media->sign_language_video_url) }}"
                                aria-describedby="{{ trim('sign-language-help '.($signErrorId ?? '')) }}"
                                @error('sign_language_video_url') aria-invalid="true" @enderror
                            >
                            <div id="sign-language-help" class="form-text">Link to an external sign-language interpretation.</div>
                            @error('sign_language_video_url')
                                <div id="sign-language-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="position" class="form-label">Position</label>
                            <input
                                id="position"
                                type="number"
                                min="1"
                                name="position"
                                class="form-control @error('position') is-invalid @enderror"
                                value="{{ old('position', $media->position) }}"
                                aria-describedby="{{ trim('position-help '.($positionErrorId ?? '')) }}"
                                @error('position') aria-invalid="true" @enderror
                            >
                            <div id="position-help" class="form-text">Update ordering position if needed.</div>
                            @error('position')
                                <div id="media-position-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save Media</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
