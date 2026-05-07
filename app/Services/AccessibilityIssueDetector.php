<?php

namespace App\Services;

use App\Models\AccessibilityIssue;
use App\Models\Survey;
use App\Models\SurveyAccessibilitySetting;
use App\Models\SurveyMedia;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;

class AccessibilityIssueDetector
{
    public const ISSUE_MISSING_TITLE = 'missing_title';
    public const ISSUE_MISSING_QUESTION_PROMPT = 'missing_question_prompt';
    public const ISSUE_MCQ_INSUFFICIENT_OPTIONS = 'mcq_insufficient_options';
    public const ISSUE_IMAGE_MISSING_ALT = 'image_missing_alt_text';
    public const ISSUE_KEYBOARD_NAV_DISABLED = 'keyboard_nav_disabled';
    public const ISSUE_VIDEO_MISSING_CAPTIONS = 'video_missing_captions_or_transcript';
    public const ISSUE_FILE_MISSING_CONSTRAINTS = 'file_missing_constraints';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function detect(Survey $survey, bool $persist = false): array
    {
        $survey->loadMissing(['questions.options', 'media', 'accessibilitySettings']);

        $issues = [];

        if (trim((string) $survey->title) === '') {
            $issues[] = $this->buildIssue(
                self::ISSUE_MISSING_TITLE,
                'error',
                'Survey title is missing.'
            );
        }

        foreach ($survey->questions as $question) {
            if (trim((string) $question->prompt) === '') {
                $issues[] = $this->buildIssue(
                    self::ISSUE_MISSING_QUESTION_PROMPT,
                    'error',
                    "Question #{$question->position} is missing a prompt.",
                    $question
                );
            }

            if ($question->requiresOptions() && $question->options->count() < 2) {
                $issues[] = $this->buildIssue(
                    self::ISSUE_MCQ_INSUFFICIENT_OPTIONS,
                    'error',
                    "Question #{$question->position} needs at least 2 options.",
                    $question
                );
            }

            if ($question->type === SurveyQuestion::TYPE_FILE) {
                $settings = is_array($question->settings_json) ? $question->settings_json : [];
                $hasConstraints = array_key_exists('allowed_types', $settings)
                    || array_key_exists('max_size_kb', $settings);

                if (! $hasConstraints) {
                    $issues[] = $this->buildIssue(
                        self::ISSUE_FILE_MISSING_CONSTRAINTS,
                        'warning',
                        "Question #{$question->position} (file upload) is missing file constraints.",
                        $question
                    );
                }
            }
        }

        foreach ($survey->media as $media) {
            if ($media->media_type === 'image' && trim((string) $media->alt_text) === '') {
                $issues[] = $this->buildIssue(
                    self::ISSUE_IMAGE_MISSING_ALT,
                    'error',
                    $this->imageAltMessage($media)
                );
            }

            if ($media->media_type === 'video' && ! $media->caption_path && ! $media->transcript_text) {
                $issues[] = $this->buildIssue(
                    self::ISSUE_VIDEO_MISSING_CAPTIONS,
                    'warning',
                    $this->videoCaptionMessage($media)
                );
            }
        }

        $settings = $survey->accessibilitySettings;

        if (! $settings) {
            $settings = $survey->accessibilitySettings()->firstOrCreate([], SurveyAccessibilitySetting::defaults());
        }

        if (! $settings->keyboard_nav_enforced) {
            $issues[] = $this->buildIssue(
                self::ISSUE_KEYBOARD_NAV_DISABLED,
                'warning',
                'Keyboard-only navigation enforcement is disabled.'
            );
        }

        if ($persist) {
            $this->persistIssues($survey, $issues);
        }

        return $issues;
    }

    /**
     * @param array<int, array<string, mixed>> $issues
     */
    private function persistIssues(Survey $survey, array $issues): void
    {
        $issueTypes = $this->knownIssueTypes();
        $now = now();

        DB::transaction(function () use ($survey, $issues, $issueTypes, $now): void {
            AccessibilityIssue::query()
                ->where('survey_id', $survey->id)
                ->whereIn('issue_type', $issueTypes)
                ->delete();

            foreach ($issues as $issue) {
                AccessibilityIssue::create([
                    'survey_id' => $survey->id,
                    'survey_question_id' => $issue['survey_question_id'] ?? null,
                    'issue_type' => $issue['issue_type'],
                    'severity' => $issue['severity'],
                    'status' => 'open',
                    'message' => $issue['message'],
                    'detected_at' => $now,
                    'resolved_at' => null,
                ]);
            }
        });
    }

    /**
     * @param SurveyQuestion|null $question
     * @return array<string, mixed>
     */
    private function buildIssue(string $type, string $severity, string $message, ?SurveyQuestion $question = null): array
    {
        return [
            'issue_type' => $type,
            'severity' => $severity,
            'message' => $message,
            'survey_question_id' => $question?->id,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function knownIssueTypes(): array
    {
        return [
            self::ISSUE_MISSING_TITLE,
            self::ISSUE_MISSING_QUESTION_PROMPT,
            self::ISSUE_MCQ_INSUFFICIENT_OPTIONS,
            self::ISSUE_IMAGE_MISSING_ALT,
            self::ISSUE_KEYBOARD_NAV_DISABLED,
            self::ISSUE_VIDEO_MISSING_CAPTIONS,
            self::ISSUE_FILE_MISSING_CONSTRAINTS,
        ];
    }

    private function imageAltMessage(SurveyMedia $media): string
    {
        $position = $media->position ?? 0;
        $label = $position > 0 ? "Image media #{$position}" : 'Image media item';

        return "{$label} is missing alt text.";
    }

    private function videoCaptionMessage(SurveyMedia $media): string
    {
        $position = $media->position ?? 0;
        $label = $position > 0 ? "Video media #{$position}" : 'Video media item';

        return "{$label} is missing captions or a transcript.";
    }
}
