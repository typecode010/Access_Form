<?php

namespace App\Http\Requests\Creator;

use App\Models\Survey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReorderSurveyMediaRequest extends FormRequest
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

            $actualIds = $survey->media()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            if ($submittedIds !== $actualIds) {
                $validator->errors()->add('ordered_ids', 'Ordered media IDs do not match survey media.');
            }
        });
    }
}
