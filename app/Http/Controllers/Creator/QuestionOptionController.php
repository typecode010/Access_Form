<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\StoreOptionRequest;
use App\Http\Requests\Creator\UpdateOptionRequest;
use App\Models\QuestionOption;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class QuestionOptionController extends Controller
{
    /**
     * Store a new option for a question.
     */
    public function store(StoreOptionRequest $request, Survey $survey, SurveyQuestion $question): RedirectResponse
    {
        $this->ensureOptionAccess($survey, $question);
        $validated = $request->validated();

        DB::transaction(function () use ($question, $validated): void {
            $targetPosition = $this->resolveCreatePosition($question, $validated['position'] ?? null);
            $this->shiftOptionsDownFrom($question, $targetPosition);

            $question->options()->create([
                'option_text' => $validated['option_text'],
                'option_value' => $validated['option_value'] ?? null,
                'position' => $targetPosition,
            ]);
        });

        return redirect()
            ->route('creator.surveys.questions.edit', [$survey, $question])
            ->with('status', 'Option added successfully.');
    }

    /**
     * Update an existing option.
     */
    public function update(UpdateOptionRequest $request, Survey $survey, SurveyQuestion $question, QuestionOption $option): RedirectResponse
    {
        $this->ensureOptionRecordAccess($survey, $question, $option);
        $validated = $request->validated();

        DB::transaction(function () use ($question, $option, $validated): void {
            $option->fill([
                'option_text' => $validated['option_text'],
                'option_value' => $validated['option_value'] ?? null,
            ]);

            $targetPosition = $this->resolveUpdatePosition(
                $question,
                $option,
                $validated['position'] ?? $option->position
            );

            if ($targetPosition !== (int) $option->position) {
                $this->moveOptionToPosition($question, $option, $targetPosition);
            }

            $option->position = $targetPosition;
            $option->save();
        });

        return redirect()
            ->route('creator.surveys.questions.edit', [$survey, $question])
            ->with('status', 'Option updated successfully.');
    }

    /**
     * Delete an option with defensive minimum check.
     */
    public function destroy(Survey $survey, SurveyQuestion $question, QuestionOption $option): RedirectResponse
    {
        $this->ensureOptionRecordAccess($survey, $question, $option);

        if ($question->requiresOptions() && $question->options()->count() <= 2) {
            return redirect()
                ->route('creator.surveys.questions.edit', [$survey, $question])
                ->with('error', 'Multiple choice questions should keep at least 2 options.');
        }

        DB::transaction(function () use ($question, $option): void {
            $option->delete();
            $this->normalizeOptionPositions($question);
        });

        return redirect()
            ->route('creator.surveys.questions.edit', [$survey, $question])
            ->with('status', 'Option deleted successfully.');
    }

    private function ensureOptionAccess(Survey $survey, SurveyQuestion $question): void
    {
        $this->ensureSurveyOwnership($survey);

        abort_unless((int) $question->survey_id === (int) $survey->id, 403, 'You are not allowed to access this question.');
        abort_unless($question->requiresOptions(), 422, 'Options can only be managed for multiple choice questions.');
    }

    private function ensureOptionRecordAccess(Survey $survey, SurveyQuestion $question, QuestionOption $option): void
    {
        $this->ensureOptionAccess($survey, $question);

        abort_unless((int) $option->survey_question_id === (int) $question->id, 403, 'You are not allowed to access this option.');
    }

    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }

    private function resolveCreatePosition(SurveyQuestion $question, ?int $requestedPosition): int
    {
        $maxPosition = (int) $question->options()->max('position');

        if ($requestedPosition === null) {
            return $maxPosition + 1;
        }

        return max(1, min($requestedPosition, $maxPosition + 1));
    }

    private function resolveUpdatePosition(SurveyQuestion $question, QuestionOption $option, int $requestedPosition): int
    {
        $maxPosition = (int) $question->options()->max('position');
        $maxPosition = max(1, $maxPosition);

        return max(1, min($requestedPosition, $maxPosition));
    }

    private function shiftOptionsDownFrom(SurveyQuestion $question, int $fromPosition): void
    {
        QuestionOption::query()
            ->where('survey_question_id', $question->id)
            ->where('position', '>=', $fromPosition)
            ->increment('position');
    }

    private function moveOptionToPosition(SurveyQuestion $question, QuestionOption $option, int $targetPosition): void
    {
        $currentPosition = (int) $option->position;

        if ($targetPosition < $currentPosition) {
            QuestionOption::query()
                ->where('survey_question_id', $question->id)
                ->whereBetween('position', [$targetPosition, $currentPosition - 1])
                ->increment('position');

            return;
        }

        QuestionOption::query()
            ->where('survey_question_id', $question->id)
            ->whereBetween('position', [$currentPosition + 1, $targetPosition])
            ->decrement('position');
    }

    private function normalizeOptionPositions(SurveyQuestion $question): void
    {
        $question->options()
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->each(function (QuestionOption $option, int $index): void {
                $newPosition = $index + 1;

                if ((int) $option->position !== $newPosition) {
                    $option->update(['position' => $newPosition]);
                }
            });
    }
}
