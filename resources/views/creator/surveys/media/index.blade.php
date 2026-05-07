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
                <a href="{{ route('creator.surveys.preview', $survey) }}" class="btn btn-outline-success">Preview Survey</a>
                <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-outline-primary">Back to Survey</a>
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

        <div class="card mb-4">
            <div class="card-body">
                <h3 class="h5 mb-3">Add Media</h3>
                <form method="POST" action="{{ route('creator.surveys.media.store', $survey) }}" enctype="multipart/form-data" novalidate>
                    @csrf

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
                                <option value="image" @selected(old('media_type') === 'image')>Image</option>
                                <option value="video" @selected(old('media_type') === 'video')>Video</option>
                                <option value="audio" @selected(old('media_type') === 'audio')>Audio</option>
                                <option value="other" @selected(old('media_type') === 'other')>Other</option>
                            </select>
                            <div id="media-type-help" class="form-text">Choose the primary media format.</div>
                            @error('media_type')
                                <div id="media-type-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="media_file" class="form-label">Upload File</label>
                            <input
                                id="media_file"
                                type="file"
                                name="media_file"
                                class="form-control @error('media_file') is-invalid @enderror"
                                aria-describedby="{{ trim('media-file-help '.($fileErrorId ?? '')) }}"
                                required
                                @error('media_file') aria-invalid="true" @enderror
                            >
                            <div id="media-file-help" class="form-text">
                                Supported formats: images (jpg/png/webp/gif), video (mp4/webm/ogg), audio (mp3/wav/ogg), other (pdf/txt/doc/docx).
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
                                value="{{ old('alt_text') }}"
                                aria-describedby="{{ trim('alt-text-help '.($altErrorId ?? '')) }}"
                                @error('alt_text') aria-invalid="true" @enderror
                            >
                            <div id="alt-text-help" class="form-text">Required for images before publishing.</div>
                            @error('alt_text')
                                <div id="alt-text-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="caption_file" class="form-label">Caption File (optional)</label>
                            <input
                                id="caption_file"
                                type="file"
                                name="caption_file"
                                class="form-control @error('caption_file') is-invalid @enderror"
                                aria-describedby="{{ trim('caption-file-help '.($captionErrorId ?? '')) }}"
                                @error('caption_file') aria-invalid="true" @enderror
                            >
                            <div id="caption-file-help" class="form-text">Upload a .vtt, .srt, or .txt caption file for video.</div>
                            @error('caption_file')
                                <div id="caption-file-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="transcript_text" class="form-label">Transcript (optional)</label>
                            <textarea
                                id="transcript_text"
                                name="transcript_text"
                                rows="3"
                                class="form-control @error('transcript_text') is-invalid @enderror"
                                aria-describedby="{{ trim('transcript-help '.($transcriptErrorId ?? '')) }}"
                                @error('transcript_text') aria-invalid="true" @enderror
                            >{{ old('transcript_text') }}</textarea>
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
                                value="{{ old('sign_language_video_url') }}"
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
                                value="{{ old('position') }}"
                                aria-describedby="{{ trim('position-help '.($positionErrorId ?? '')) }}"
                                @error('position') aria-invalid="true" @enderror
                            >
                            <div id="position-help" class="form-text">Leave blank to append to the end.</div>
                            @error('position')
                                <div id="media-position-error" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Add Media</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="h5 mb-3">Media Library</h3>

                @if ($mediaItems->isEmpty())
                    <p class="text-muted mb-0">No media added yet. Use the form above to upload media.</p>
                @else
                    @php
                        $orderedIds = $mediaItems->pluck('id')->values()->all();
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <caption class="visually-hidden">Media items for {{ $survey->title }}</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Position</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">File</th>
                                    <th scope="col">Alt Text</th>
                                    <th scope="col">Captions / Transcript</th>
                                    <th scope="col">Sign Language</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mediaItems as $index => $media)
                                    @php
                                        $moveUpIds = $orderedIds;
                                        if ($index > 0) {
                                            $temp = $moveUpIds[$index - 1];
                                            $moveUpIds[$index - 1] = $moveUpIds[$index];
                                            $moveUpIds[$index] = $temp;
                                        }

                                        $moveDownIds = $orderedIds;
                                        if ($index < count($orderedIds) - 1) {
                                            $temp = $moveDownIds[$index + 1];
                                            $moveDownIds[$index + 1] = $moveDownIds[$index];
                                            $moveDownIds[$index] = $temp;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $media->position }}</td>
                                        <td class="text-capitalize">{{ $media->media_type }}</td>
                                        <td>
                                            @if ($media->file_path)
                                                <a href="{{ Storage::disk('public')->url($media->file_path) }}" target="_blank" rel="noopener">View file</a>
                                            @else
                                                <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $media->alt_text ?: 'Missing' }}
                                        </td>
                                        <td>
                                            <div class="vstack gap-1">
                                                @if ($media->caption_path)
                                                    <a href="{{ Storage::disk('public')->url($media->caption_path) }}" target="_blank" rel="noopener">Caption file</a>
                                                @endif
                                                @if ($media->transcript_text)
                                                    <span class="text-muted small">{{ \Illuminate\Support\Str::limit($media->transcript_text, 100) }}</span>
                                                @endif
                                                @if (! $media->caption_path && ! $media->transcript_text)
                                                    <span class="text-muted">None provided</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($media->sign_language_video_url)
                                                <a href="{{ $media->sign_language_video_url }}" target="_blank" rel="noopener">View link</a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                <a href="{{ route('creator.surveys.media.edit', [$survey, $media]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form method="POST" action="{{ route('creator.surveys.media.destroy', [$survey, $media]) }}" onsubmit="return confirm('Delete this media item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <form method="POST" action="{{ route('creator.surveys.media.reorder', $survey) }}">
                                                    @csrf
                                                    @foreach ($moveUpIds as $orderedId)
                                                        <input type="hidden" name="ordered_ids[]" value="{{ $orderedId }}">
                                                    @endforeach
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled($index === 0)>Move Up</button>
                                                </form>
                                                <form method="POST" action="{{ route('creator.surveys.media.reorder', $survey) }}">
                                                    @csrf
                                                    @foreach ($moveDownIds as $orderedId)
                                                        <input type="hidden" name="ordered_ids[]" value="{{ $orderedId }}">
                                                    @endforeach
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled($index === count($orderedIds) - 1)>Move Down</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
