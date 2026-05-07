@php
    $warningCount = $warningCount ?? 0;
    $filterText = strtolower(trim($survey->title.' '.$survey->public_slug.' '.$survey->status));
    $badgeClass = $survey->status === 'published'
        ? 'text-bg-success'
        : ($survey->status === 'archived' ? 'text-bg-secondary' : 'text-bg-warning');
@endphp

<tr data-survey-row data-survey-text="{{ $filterText }}">
    <td>
        <div class="fw-semibold">{{ $survey->title }}</div>
        <div class="af-muted small">{{ $survey->public_slug }}</div>
    </td>
    <td>
        <span class="badge af-status-badge {{ $badgeClass }}">{{ $survey->status }}</span>
    </td>
    <td>
        @include('creator.partials._a11y_badge', ['count' => $warningCount])
    </td>
    <td>
        {{ $survey->updated_at?->diffForHumans() ?? 'N/A' }}
    </td>
    <td>
        <div class="af-action-group">
            <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            <a href="{{ route('creator.surveys.questions.index', $survey) }}" class="btn btn-sm btn-outline-dark">Builder</a>
            <a href="{{ route('creator.surveys.preview', $survey) }}" class="btn btn-sm btn-outline-success">Preview</a>
            <a href="{{ route('creator.surveys.accessibility.edit', $survey) }}" class="btn btn-sm btn-outline-secondary">Accessibility</a>
            <a href="{{ route('creator.surveys.responses.index', $survey) }}" class="btn btn-sm btn-outline-info">Responses</a>
        </div>
    </td>
</tr>
