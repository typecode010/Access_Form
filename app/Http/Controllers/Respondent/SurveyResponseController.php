<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Models\Survey;
use App\Models\SurveyAccessibilitySetting;
use App\Models\SurveyQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SurveyResponseController extends Controller
{
    public function show(string $slug): View
    {
        $survey = Survey::query()
            ->where('public_slug', $slug)
            ->where('status', 'published')
            ->with(['questions.options', 'accessibilitySettings', 'media'])
            ->firstOrFail();

        $settings = $survey->accessibilitySettings()
            ->firstOrCreate([], SurveyAccessibilitySetting::defaults());

        $survey->setRelation('accessibilitySettings', $settings);

        return view('respondent.surveys.public', [
            'pageTitle' => $survey->title,
            'survey' => $survey,
            'questions' => $survey->questions,
            'settings' => $settings,
        ]);
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        $survey = Survey::query()
            ->where('public_slug', $slug)
            ->where('status', 'published')
            ->with(['questions.options', 'accessibilitySettings'])
            ->firstOrFail();

        $rules = $this->buildValidationRules($survey);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('surveys.public.show', $survey->public_slug)
                ->withErrors($validator)
                ->withInput();
        }

        $response = Response::create([
            'survey_id' => $survey->id,
            'respondent_id' => auth()->id(),
            'channel' => 'web',
            'submitted_at' => now(),
        ]);

        foreach ($survey->questions as $question) {
            $this->storeAnswer($response, $question, $request);
        }

        return redirect()->route('surveys.public.thanks', $survey->public_slug);
    }

    public function thanks(string $slug): View
    {
        $survey = Survey::query()
            ->where('public_slug', $slug)
            ->select(['id', 'title', 'public_slug'])
            ->firstOrFail();

        return view('respondent.surveys.thanks', [
            'pageTitle' => 'Submission received',
            'survey' => $survey,
        ]);
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\Exists>>
     */
    private function buildValidationRules(Survey $survey): array
    {
        $rules = [];

        foreach ($survey->questions as $question) {
            $answerKey = 'answers.'.$question->id;

            if ($question->type === SurveyQuestion::TYPE_MULTIPLE_CHOICE) {
                $rules[$answerKey] = [
                    $question->is_required ? 'required' : 'nullable',
                    'integer',
                    Rule::exists('question_options', 'id')->where('survey_question_id', $question->id),
                ];
                continue;
            }

            if ($question->type === SurveyQuestion::TYPE_TEXT) {
                $rules[$answerKey] = [
                    $question->is_required ? 'required' : 'nullable',
                    'string',
                    'max:5000',
                ];
                continue;
            }

            if ($question->type === SurveyQuestion::TYPE_RATING) {
                $settings = is_array($question->settings_json) ? $question->settings_json : [];
                $min = isset($settings['min']) ? (int) $settings['min'] : 1;
                $max = isset($settings['max']) ? (int) $settings['max'] : 5;
                $min = max(1, $min);
                $max = max($min, $max);

                $rules[$answerKey] = [
                    $question->is_required ? 'required' : 'nullable',
                    'integer',
                    'min:'.$min,
                    'max:'.$max,
                ];
                continue;
            }

            if ($question->type === SurveyQuestion::TYPE_FILE) {
                $fileKey = 'files.'.$question->id;
                $settings = is_array($question->settings_json) ? $question->settings_json : [];
                $fileRules = [$question->is_required ? 'required' : 'nullable', 'file'];

                if (! empty($settings['max_size_kb'])) {
                    $fileRules[] = 'max:'.(int) $settings['max_size_kb'];
                }

                if (! empty($settings['allowed_types']) && is_array($settings['allowed_types'])) {
                    $fileRules[] = 'mimes:'.implode(',', $settings['allowed_types']);
                }

                $rules[$fileKey] = $fileRules;
            }
        }

        return $rules;
    }

    private function storeAnswer(Response $response, SurveyQuestion $question, Request $request): void
    {
        if ($question->type === SurveyQuestion::TYPE_FILE) {
            $file = $request->file('files.'.$question->id);

            if (! $file) {
                return;
            }

            $path = $file->store('responses/'.$response->survey_id, 'public');

            ResponseAnswer::create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'answer_file_path' => $path,
                'answer_meta_json' => [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                ],
            ]);

            return;
        }

        $value = $request->input('answers.'.$question->id);

        if ($value === null || $value === '') {
            return;
        }

        if ($question->type === SurveyQuestion::TYPE_MULTIPLE_CHOICE) {
            $option = $question->options()->whereKey($value)->first();

            ResponseAnswer::create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'answer_text' => $option?->option_text ?? (string) $value,
                'answer_meta_json' => [
                    'option_id' => (int) $value,
                    'option_value' => $option?->option_value,
                ],
            ]);

            return;
        }

        ResponseAnswer::create([
            'response_id' => $response->id,
            'question_id' => $question->id,
            'answer_text' => (string) $value,
        ]);
    }
}
