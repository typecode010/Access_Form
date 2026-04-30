<?php

namespace App\Http\Requests\Creator;

use App\Models\Survey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReorderQuestionsRequest extends FormRequest
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
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['required', 'integer', 'distinct'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ordered_question_ids') && ! $this->has('ordered_ids')) {
            $this->merge([
                'ordered_ids' => $this->input('ordered_question_ids'),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $survey = $this->route('survey');

            if (! $survey instanceof Survey) {
                return;
            }

            $submittedIds = collect($this->input('ordered_ids', []))
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            $actualIds = $survey->questions()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            if ($submittedIds !== $actualIds) {
                $validator->errors()->add('ordered_ids', 'Ordered question IDs do not match survey questions.');
            }
        });
    }
}
