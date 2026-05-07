@php
    $contrastErrorId = 'pref-contrast-help';
    $dyslexiaErrorId = 'pref-dyslexia-help';
    $textSizeHelpId = 'pref-text-size-help';
    $motionHelpId = 'pref-motion-help';
@endphp

<form class="d-grid gap-3" data-a11y-preferences>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="pref-contrast" data-pref-contrast aria-describedby="{{ $contrastErrorId }}">
        <label class="form-check-label" for="pref-contrast">High contrast theme</label>
        <div id="{{ $contrastErrorId }}" class="form-text">Use stronger contrast for readability.</div>
    </div>

    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="pref-dyslexia" data-pref-dyslexia aria-describedby="{{ $dyslexiaErrorId }}">
        <label class="form-check-label" for="pref-dyslexia">Dyslexia-friendly typography</label>
        <div id="{{ $dyslexiaErrorId }}" class="form-text">Adds spacing and line-height improvements.</div>
    </div>

    <div>
        <label for="pref-text-size" class="form-label">Text size</label>
        <select id="pref-text-size" class="form-select" data-pref-text-size aria-describedby="{{ $textSizeHelpId }}">
            <option value="sm">Small</option>
            <option value="md" selected>Medium</option>
            <option value="lg">Large</option>
        </select>
        <div id="{{ $textSizeHelpId }}" class="form-text">Adjust base font size for this dashboard.</div>
    </div>

    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="pref-motion" data-pref-motion aria-describedby="{{ $motionHelpId }}">
        <label class="form-check-label" for="pref-motion">Reduce motion</label>
        <div id="{{ $motionHelpId }}" class="form-text">Minimize animation and motion effects.</div>
    </div>

    <div class="visually-hidden" role="status" aria-live="polite" data-prefs-status></div>
</form>
