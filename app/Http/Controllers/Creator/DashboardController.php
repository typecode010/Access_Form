<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $creatorId = auth()->id();

        $baseQuery = Survey::query()->where('creator_id', $creatorId);

        $totalSurveys = (clone $baseQuery)->count();
        $draftSurveys = (clone $baseQuery)->where('status', 'draft')->count();
        $publishedSurveys = (clone $baseQuery)->where('status', 'published')->count();

        $latestSurvey = (clone $baseQuery)->latest('updated_at')->first();

        $allSurveys = (clone $baseQuery)
            ->with(['questions.options', 'accessibilitySettings'])
            ->get();

        $surveys = $allSurveys
            ->sortByDesc('updated_at')
            ->take(10)
            ->values();

        [$warningSummary, $surveyWarningCounts] = $this->buildWarningSummary($allSurveys);

        $recentActivity = $surveys->map(function (Survey $survey): array {
            $action = $survey->created_at && $survey->updated_at && $survey->created_at->equalTo($survey->updated_at)
                ? 'Created'
                : 'Updated';

            return [
                'action' => $action,
                'title' => $survey->title,
                'time' => $survey->updated_at,
            ];
        });

        return view('creator.dashboard', [
            'pageTitle' => 'Form Creator Dashboard',
            'roleName' => 'FormCreator',
            'totalSurveys' => $totalSurveys,
            'draftSurveys' => $draftSurveys,
            'publishedSurveys' => $publishedSurveys,
            'latestSurvey' => $latestSurvey,
            'surveys' => $surveys,
            'surveyWarningCounts' => $surveyWarningCounts,
            'warningSummary' => $warningSummary,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, int>}
     */
    private function buildWarningSummary(Collection $surveys): array
    {
        $warningLabels = [
            'keyboard_nav' => 'Keyboard-only navigation disabled',
            'missing_prompt' => 'Missing question prompt',
            'mcq_options' => 'Multiple choice has fewer than 2 options',
            'no_questions' => 'No questions added',
        ];

        $warningCounts = array_fill_keys(array_keys($warningLabels), 0);
        $surveyWarningCounts = [];
        $surveysWithWarnings = 0;

        foreach ($surveys as $survey) {
            $warnings = $this->surveyWarningKeys($survey);
            $surveyWarningCounts[$survey->id] = count($warnings);

            if (count($warnings) > 0) {
                $surveysWithWarnings++;
            }

            foreach ($warnings as $key) {
                $warningCounts[$key]++;
            }
        }

        $topWarnings = collect($warningCounts)
            ->filter(fn (int $count) => $count > 0)
            ->sortDesc()
            ->take(3)
            ->map(fn (int $count, string $key) => [
                'label' => $warningLabels[$key] ?? $key,
                'count' => $count,
            ])
            ->values()
            ->all();

        return [
            [
                'surveysWithWarnings' => $surveysWithWarnings,
                'topWarnings' => $topWarnings,
            ],
            $surveyWarningCounts,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function surveyWarningKeys(Survey $survey): array
    {
        $warnings = [];

        $settings = $survey->accessibilitySettings;

        if (! $settings || ! $settings->keyboard_nav_enforced) {
            $warnings[] = 'keyboard_nav';
        }

        if ($survey->questions->isEmpty()) {
            $warnings[] = 'no_questions';

            return $warnings;
        }

        $missingPrompt = false;
        $mcqNeedsOptions = false;

        foreach ($survey->questions as $question) {
            if (! $missingPrompt && trim((string) $question->prompt) === '') {
                $missingPrompt = true;
            }

            if (! $mcqNeedsOptions && $question->type === SurveyQuestion::TYPE_MULTIPLE_CHOICE) {
                if ($question->options->count() < 2) {
                    $mcqNeedsOptions = true;
                }
            }

            if ($missingPrompt && $mcqNeedsOptions) {
                break;
            }
        }

        if ($missingPrompt) {
            $warnings[] = 'missing_prompt';
        }

        if ($mcqNeedsOptions) {
            $warnings[] = 'mcq_options';
        }

        return $warnings;
    }
}
