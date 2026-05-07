@php
    $context = $context ?? 'public';
    $isPreview = $context === 'preview';
@endphp

@if ($mediaItems->isNotEmpty())
    <section class="mb-4" aria-labelledby="survey-media-title">
        <h3 id="survey-media-title" class="h5">Survey media</h3>
        <div class="vstack gap-3 af-media-stack">
            @foreach ($mediaItems as $media)
                <article class="card af-media-card">
                    <div class="card-body">
                        <h4 class="h6 text-capitalize">{{ $media->media_type }} media</h4>

                        @if ($media->media_type === 'image')
                            <figure class="mb-0">
                                <img
                                    src="{{ Storage::disk('public')->url($media->file_path) }}"
                                    alt="{{ $media->alt_text ?? '' }}"
                                    class="img-fluid rounded border"
                                >
                                @if ($isPreview)
                                    <figcaption class="small mt-2">
                                        @if ($media->alt_text)
                                            Alt text: {{ $media->alt_text }}
                                        @else
                                            <span class="text-danger">Alt text missing for this image.</span>
                                        @endif
                                    </figcaption>
                                @endif
                            </figure>
                        @elseif ($media->media_type === 'video')
                            <video controls class="w-100 rounded border">
                                <source src="{{ Storage::disk('public')->url($media->file_path) }}">
                                Your browser does not support video playback.
                            </video>
                        @elseif ($media->media_type === 'audio')
                            <audio controls class="w-100">
                                <source src="{{ Storage::disk('public')->url($media->file_path) }}">
                                Your browser does not support audio playback.
                            </audio>
                        @else
                            <a href="{{ Storage::disk('public')->url($media->file_path) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                                Download file
                            </a>
                        @endif

                        @if ($media->caption_path || $media->transcript_text || $media->sign_language_video_url)
                            <div class="mt-3">
                                <ul class="list-unstyled small mb-0">
                                    @if ($media->caption_path)
                                        <li>
                                            <strong>Captions:</strong>
                                            <a href="{{ Storage::disk('public')->url($media->caption_path) }}" target="_blank" rel="noopener">Download caption file</a>
                                        </li>
                                    @endif
                                    @if ($media->transcript_text)
                                        <li>
                                            <strong>Transcript:</strong>
                                            <div class="border rounded p-2 mt-1 af-media-transcript">{{ $media->transcript_text }}</div>
                                        </li>
                                    @endif
                                    @if ($media->sign_language_video_url)
                                        <li>
                                            <strong>Sign-language video:</strong>
                                            <a href="{{ $media->sign_language_video_url }}" target="_blank" rel="noopener">Open link</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @elseif ($isPreview && $media->media_type === 'video')
                            <p class="text-warning small mt-2">Captions or a transcript are missing for this video.</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
