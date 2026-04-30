<?php

namespace App\Http\Requests\Creator;

use App\Models\SurveyQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateOptionRequest extends FormRequest
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
            'option_text' => ['required', 'string', 'max:255'],
            'option_value' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'option_text' => $this->trimText($this->input('option_text')),
            'option_value' => $this->trimText($this->input('option_value')),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $question = $this->route('question');

            if ($question instanceof SurveyQuestion && ! $question->requiresOptions()) {
                $validator->errors()->add('option_text', 'Options can only be managed for multiple choice questions.');
            }
        });
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
