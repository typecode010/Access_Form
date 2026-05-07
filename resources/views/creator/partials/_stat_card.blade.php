@props(['label', 'value', 'helper' => null, 'cardId'])

<article class="card af-card h-100" aria-labelledby="{{ $cardId }}">
    <div class="card-body">
        <h3 id="{{ $cardId }}" class="h6 text-uppercase af-muted">{{ $label }}</h3>
        <p class="af-stat-value">{{ $value }}</p>
        @if ($helper)
            <p class="mb-0 af-muted">{{ $helper }}</p>
        @endif
    </div>
</article>
