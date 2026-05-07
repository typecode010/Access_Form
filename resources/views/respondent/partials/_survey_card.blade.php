@php
    $settings = $survey->accessibilitySettings;
    $hasContrast = (bool) ($settings?->high_contrast_enabled);
    $hasDyslexia = (bool) ($settings?->dyslexia_friendly_enabled);
    $hasThemes = $hasContrast || $hasDyslexia;
    $questionsCount = (int) ($survey->questions_count ?? 0);
    $estimatedMinutes = max(2, (int) ceil($questionsCount / 2));
    $creatorName = $survey->creator?->name ?? 'AccessForm Team';
    $description = $survey->description ?: 'No description provided yet.';
    $filterText = strtolower(trim($survey->title.' '.$survey->public_slug.' '.$description));
@endphp

<article
    class="card af-card respondent-survey-card"
    aria-labelledby="survey-title-{{ $survey->id }}"
    data-survey-card
    data-title="{{ $filterText }}"
    data-contrast="{{ $hasContrast ? '1' : '0' }}"
    data-dyslexia="{{ $hasDyslexia ? '1' : '0' }}"
    data-updated="{{ $survey->updated_at?->timestamp ?? 0 }}"
>
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h3 id="survey-title-{{ $survey->id }}" class="h5 mb-1">{{ $survey->title }}</h3>
                <p class="af-muted small mb-2">By {{ $creatorName }}</p>
            </div>
            @if ($hasThemes)
                <span class="badge text-bg-success">Accessible themes available</span>
            @else
                <span class="badge text-bg-secondary">Default theme</span>
            @endif
        </div>

        <p class="mb-2">{{ $description }}</p>
        <p class="af-muted small mb-3">Estimated time: {{ $estimatedMinutes }} min</p>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ url('/f/'.$survey->public_slug) }}" class="btn btn-primary">Open Survey</a>
            <span class="af-muted small">Slug: <code>{{ $survey->public_slug }}</code></span>
        </div>
    </div>
</article>
