@props(['total'])

<div class="af-filter-bar" aria-labelledby="available-surveys-title">
    <div class="row g-3 align-items-end">
        <div class="col-md-5">
            <label for="survey-search" class="form-label">Search surveys</label>
            <input
                id="survey-search"
                type="text"
                class="form-control"
                placeholder="Search by title or description"
                data-respondent-filter
                aria-describedby="survey-search-help survey-filter-status"
            >
            <div id="survey-search-help" class="form-text">Filters the surveys shown below.</div>
            <div id="survey-filter-status" class="visually-hidden" role="status" aria-live="polite" data-survey-status></div>
        </div>

        <div class="col-md-4">
            <fieldset class="border rounded p-2">
                <legend class="fs-6 mb-1">Accessibility filters</legend>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="filter-contrast" data-filter-contrast>
                    <label class="form-check-label" for="filter-contrast">High contrast available</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="filter-dyslexia" data-filter-dyslexia>
                    <label class="form-check-label" for="filter-dyslexia">Dyslexia mode available</label>
                </div>
            </fieldset>
        </div>

        <div class="col-md-3">
            <label for="survey-sort" class="form-label">Sort by</label>
            <select id="survey-sort" class="form-select" data-survey-sort>
                <option value="newest">Newest first</option>
                <option value="oldest">Oldest first</option>
            </select>
        </div>
    </div>
    <p class="af-muted small mt-2">Showing {{ $total }} published survey{{ $total === 1 ? '' : 's' }} (current page).</p>
</div>
