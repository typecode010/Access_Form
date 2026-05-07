@php
    $survey = $response->survey;
    $surveyTitle = $survey?->title ?? 'Survey';
    $slug = $survey?->public_slug;
    $submittedAt = $response->submitted_at ? $response->submitted_at->toDayDateTimeString() : 'In progress';
    $statusLabel = $response->submitted_at ? 'Submitted' : 'In progress';
    $statusClass = $response->submitted_at ? 'text-bg-success' : 'text-bg-warning';
    $answersCount = (int) ($response->answers_count ?? 0);
@endphp

<tr>
    <td>
        <div class="fw-semibold">{{ $surveyTitle }}</div>
        <div class="af-muted small">{{ $slug ?? 'N/A' }}</div>
    </td>
    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
    <td>{{ $submittedAt }}</td>
    <td>{{ $answersCount }}</td>
    <td>
        @if ($slug)
            <a class="btn btn-sm btn-outline-primary" href="{{ route('surveys.public.show', $slug) }}" aria-label="Open {{ $surveyTitle }}">Open</a>
        @else
            <span class="af-muted">N/A</span>
        @endif
    </td>
</tr>
