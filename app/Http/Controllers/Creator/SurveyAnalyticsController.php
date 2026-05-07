<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Export;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SurveyAnalyticsController extends Controller
{
    /**
     * Display analytics for a survey.
     */
    public function show(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        $survey->load(['questions.options']);

        $totalResponses = Response::query()
            ->where('survey_id', $survey->id)
            ->count();

        $latestResponse = Response::query()
            ->where('survey_id', $survey->id)
            ->latest('submitted_at')
            ->first();

        $responsesPerDay = Response::query()
            ->where('survey_id', $survey->id)
            ->whereNotNull('submitted_at')
            ->selectRaw('DATE(submitted_at) as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $questionIds = $survey->questions->pluck('id');

        $answers = ResponseAnswer::query()
            ->whereIn('question_id', $questionIds)
            ->with('response')
            ->get();

        $answersByQuestion = $answers->groupBy('question_id');

        $questionSummaries = $survey->questions->map(function (SurveyQuestion $question) use ($answersByQuestion): array {
            $answersForQuestion = $answersByQuestion->get($question->id, collect());

            $summary = [
                'question' => $question,
                'type' => $question->type,
                'answer_count' => $answersForQuestion->count(),
                'latest_samples' => [],
                'option_counts' => [],
                'rating_stats' => null,
                'file_count' => null,
            ];

            if ($question->type === SurveyQuestion::TYPE_TEXT) {
                $summary['latest_samples'] = $this->latestTextSamples($answersForQuestion);
            }

            if ($question->type === SurveyQuestion::TYPE_MULTIPLE_CHOICE) {
                $summary['option_counts'] = $question->options->map(function ($option) use ($answersForQuestion): array {
                    $count = $answersForQuestion
                        ->filter(function ($answer) use ($option): bool {
                            $meta = is_array($answer->answer_meta_json) ? $answer->answer_meta_json : [];

                            return (int) ($meta['option_id'] ?? 0) === (int) $option->id;
                        })
                        ->count();

                    return [
                        'label' => $option->option_text,
                        'count' => $count,
                    ];
                })->values()->all();
            }

            if ($question->type === SurveyQuestion::TYPE_RATING) {
                $values = $answersForQuestion
                    ->map(fn ($answer) => is_numeric($answer->answer_text) ? (float) $answer->answer_text : null)
                    ->filter(fn ($value) => $value !== null)
                    ->values();

                $summary['rating_stats'] = [
                    'avg' => $values->isEmpty() ? null : round($values->avg(), 2),
                    'min' => $values->isEmpty() ? null : $values->min(),
                    'max' => $values->isEmpty() ? null : $values->max(),
                ];
            }

            if ($question->type === SurveyQuestion::TYPE_FILE) {
                $summary['file_count'] = $answersForQuestion
                    ->filter(fn ($answer) => ! empty($answer->answer_file_path))
                    ->count();
            }

            return $summary;
        })->values();

        $exports = Export::query()
            ->where('survey_id', $survey->id)
            ->orderByDesc('generated_at')
            ->take(10)
            ->get();

        return view('creator.surveys.analytics.index', [
            'pageTitle' => 'Survey Analytics',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'totalResponses' => $totalResponses,
            'latestResponse' => $latestResponse,
            'responsesPerDay' => $responsesPerDay,
            'questionSummaries' => $questionSummaries,
            'exports' => $exports,
        ]);
    }

    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }

    /**
     * @param Collection<int, ResponseAnswer> $answers
     * @return array<int, string>
     */
    private function latestTextSamples(Collection $answers): array
    {
        return $answers
            ->filter(fn ($answer) => ! empty($answer->answer_text))
            ->sortByDesc(fn ($answer) => $answer->response?->submitted_at ?? $answer->created_at)
            ->take(5)
            ->pluck('answer_text')
            ->values()
            ->all();
    }
}
