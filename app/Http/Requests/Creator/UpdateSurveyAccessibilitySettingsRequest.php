<?php

namespace App\Http\Requests\Creator;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyAccessibilitySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'high_contrast_enabled' => ['required', 'boolean'],
            'dyslexia_friendly_enabled' => ['required', 'boolean'],
            'keyboard_nav_enforced' => ['required', 'boolean'],
            'text_size' => ['nullable', 'in:sm,md,lg'],
            'reduced_motion' => ['required', 'boolean'],
        ];
    }
}
