<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\ReorderQuestionsRequest;
use App\Http\Requests\Creator\StoreQuestionRequest;
use App\Http\Requests\Creator\UpdateQuestionRequest;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurveyQuestionController extends Controller
{
    /**
     * Display all questions for a survey.
     */
    public function index(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        $questions = $survey->questions()
            ->with('options')
            ->get();

        return view('creator.surveys.questions.index', [
            'pageTitle' => 'Question Builder',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'questions' => $questions,
            'questionTypes' => $this->questionTypeLabels(),
        ]);
    }

    /**
     * Show create form for a new question.
     */
    public function create(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        return view('creator.surveys.questions.create', [
            'pageTitle' => 'Add Question',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'question' => new SurveyQuestion([
                'type' => SurveyQuestion::TYPE_TEXT,
                'is_required' => false,
            ]),
            'questionTypes' => $this->questionTypeLabels(),
        ]);
    }

    /**
     * Store a new survey question.
     */
    public function store(StoreQuestionRequest $request, Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);
        $validated = $request->validated();

        $question = DB::transaction(function () use ($survey, $validated): SurveyQuestion {
            $targetPosition = $this->resolveCreatePosition($survey, $validated['position'] ?? null);
            $this->shiftQuestionsDownFrom($survey, $targetPosition);

            return $survey->questions()->create([
                'type' => $validated['type'],
                'prompt' => $validated['prompt'],
                'help_text' => $validated['help_text'] ?? null,
                'is_required' => $validated['is_required'] ?? false,
                'position' => $targetPosition,
                'settings_json' => $this->decodeSettingsJson($validated['settings_json'] ?? null),
            ]);
        });

        return redirect()
            ->route('creator.surveys.questions.edit', [$survey, $question])
            ->with('status', 'Question created successfully.');
    }

    /**
     * Show edit form for a question.
     */
    public function edit(Survey $survey, SurveyQuestion $question): View
    {
        $this->ensureQuestionAccess($survey, $question);
        $question->load('options');

        return view('creator.surveys.questions.edit', [
            'pageTitle' => 'Edit Question',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'question' => $question,
            'questionTypes' => $this->questionTypeLabels(),
        ]);
    }

    /**
     * Update an existing question.
     */
    public function update(UpdateQuestionRequest $request, Survey $survey, SurveyQuestion $question): RedirectResponse
    {
        $this->ensureQuestionAccess($survey, $question);
        $validated = $request->validated();

        DB::transaction(function () use ($survey, $question, $validated): void {
            $question->fill([
                'type' => $validated['type'],
                'prompt' => $validated['prompt'],
                'help_text' => $validated['help_text'] ?? null,
                'is_required' => $validated['is_required'] ?? false,
                'settings_json' => $this->decodeSettingsJson($validated['settings_json'] ?? null),
            ]);

            $targetPosition = $this->resolveUpdatePosition(
                $survey,
                $question,
                $validated['position'] ?? $question->position
            );

            if ($targetPosition !== (int) $question->position) {
                $this->moveQuestionToPosition($survey, $question, $targetPosition);
            }

            $question->position = $targetPosition;
            $question->save();
        });

        return redirect()
            ->route('creator.surveys.questions.edit', [$survey, $question])
            ->with('status', 'Question updated successfully.');
    }

    /**
     * Delete a question and normalize positions.
     */
    public function destroy(Survey $survey, SurveyQuestion $question): RedirectResponse
    {
        $this->ensureQuestionAccess($survey, $question);

        DB::transaction(function () use ($survey, $question): void {
            $question->delete();
            $this->normalizeQuestionPositions($survey);
        });

        return redirect()
            ->route('creator.surveys.questions.index', $survey)
            ->with('status', 'Question deleted successfully.');
    }

    /**
     * Reorder questions using explicit ordered IDs.
     */
    public function reorder(ReorderQuestionsRequest $request, Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);

        $orderedIds = collect($request->validated()['ordered_ids'])
            ->map(fn ($id) => (int) $id)
            ->values();

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $questionId) {
                SurveyQuestion::query()
                    ->whereKey($questionId)
                    ->update(['position' => $index + 1]);
            }
        });

        return redirect()
            ->route('creator.surveys.questions.index', $survey)
            ->with('status', 'Question order updated successfully.');
    }

    /**
     * Ensure the authenticated creator owns the survey.
     */
    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }

    /**
     * Ensure survey ownership and question-survey relation.
     */
    private function ensureQuestionAccess(Survey $survey, SurveyQuestion $question): void
    {
        $this->ensureSurveyOwnership($survey);
        abort_unless((int) $question->survey_id === (int) $survey->id, 403, 'You are not allowed to access this question.');
    }

    /**
     * @return array<string, string>
     */
    private function questionTypeLabels(): array
    {
        return [
            SurveyQuestion::TYPE_MULTIPLE_CHOICE => 'Multiple Choice',
            SurveyQuestion::TYPE_TEXT => 'Text',
            SurveyQuestion::TYPE_RATING => 'Rating',
            SurveyQuestion::TYPE_FILE => 'File Upload',
        ];
    }

    private function resolveCreatePosition(Survey $survey, ?int $requestedPosition): int
    {
        $maxPosition = (int) $survey->questions()->max('position');

        if ($requestedPosition === null) {
            return $maxPosition + 1;
        }

        return max(1, min($requestedPosition, $maxPosition + 1));
    }

    private function resolveUpdatePosition(Survey $survey, SurveyQuestion $question, int $requestedPosition): int
    {
        $maxPosition = (int) $survey->questions()->max('position');
        $maxPosition = max(1, $maxPosition);

        return max(1, min($requestedPosition, $maxPosition));
    }

    private function shiftQuestionsDownFrom(Survey $survey, int $fromPosition): void
    {
        SurveyQuestion::query()
            ->where('survey_id', $survey->id)
            ->where('position', '>=', $fromPosition)
            ->increment('position');
    }

    private function moveQuestionToPosition(Survey $survey, SurveyQuestion $question, int $targetPosition): void
    {
        $currentPosition = (int) $question->position;

        if ($targetPosition < $currentPosition) {
            SurveyQuestion::query()
                ->where('survey_id', $survey->id)
                ->whereBetween('position', [$targetPosition, $currentPosition - 1])
                ->increment('position');

            return;
        }

        SurveyQuestion::query()
            ->where('survey_id', $survey->id)
            ->whereBetween('position', [$currentPosition + 1, $targetPosition])
            ->decrement('position');
    }

    private function normalizeQuestionPositions(Survey $survey): void
    {
        $survey->questions()
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->each(function (SurveyQuestion $question, int $index): void {
                $newPosition = $index + 1;

                if ((int) $question->position !== $newPosition) {
                    $question->update(['position' => $newPosition]);
                }
            });
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeSettingsJson(?string $settingsJson): ?array
    {
        if ($settingsJson === null) {
            return null;
        }

        $decoded = json_decode($settingsJson, true);

        return is_array($decoded) ? $decoded : null;
    }
}
