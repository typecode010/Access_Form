<?php

namespace App\Http\Requests\Creator;

use App\Models\SurveyQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
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
            'type' => ['required', Rule::in(SurveyQuestion::ALLOWED_TYPES)],
            'prompt' => ['required', 'string', 'max:5000'],
            'help_text' => ['nullable', 'string', 'max:5000'],
            'is_required' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:1'],
            'settings_json' => ['nullable', 'string', 'json', 'max:10000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'prompt' => $this->trimText($this->input('prompt')),
            'help_text' => $this->trimText($this->input('help_text')),
            'settings_json' => $this->trimText($this->input('settings_json')),
            'is_required' => $this->boolean('is_required'),
        ]);
    }

    private function trimText(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
