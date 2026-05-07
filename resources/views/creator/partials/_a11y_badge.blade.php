@props(['count' => 0])

@if ($count > 0)
    <span class="af-a11y-pill af-badge-warning" aria-label="{{ $count }} accessibility warnings">
        {{ $count }} warning{{ $count === 1 ? '' : 's' }}
    </span>
@else
    <span class="af-a11y-pill af-badge-success" aria-label="No accessibility warnings">
        A11y OK
    </span>
@endif
