<?php

namespace App\Services;

use App\Models\Export;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SurveyCsvExportService
{
    public function generate(Survey $survey, ?User $actor): Export
    {
        $survey->load(['questions', 'responses.answers']);

        $path = $this->buildExportPath($survey);
        $csvContent = $this->buildCsv($survey);

        Storage::disk('public')->put($path, $csvContent);

        return Export::create([
            'survey_id' => $survey->id,
            'format' => 'csv',
            'file_path' => $path,
            'generated_by' => $actor?->id,
            'generated_at' => now(),
            'status' => 'done',
            'error_message' => null,
        ]);
    }

    private function buildExportPath(Survey $survey): string
    {
        $slug = Str::slug($survey->title ?: 'survey');
        $timestamp = now()->format('Ymd_His');

        return "exports/surveys/{$survey->id}/{$slug}_{$timestamp}.csv";
    }

    private function buildCsv(Survey $survey): string
    {
        $questions = $survey->questions->sortBy('position')->values();
        $responses = $survey->responses->sortBy('submitted_at')->values();

        $handle = fopen('php://temp', 'r+');

        $headers = ['response_id', 'submitted_at', 'channel'];
        foreach ($questions as $question) {
            $prompt = trim((string) $question->prompt);
            $prompt = str_replace(["\r", "\n"], ' ', $prompt);
            $label = $prompt !== ''
                ? "Q{$question->position}: {$prompt}"
                : "Q{$question->position}";
            $headers[] = $label;
        }

        fputcsv($handle, $headers);

        foreach ($responses as $response) {
            $answersByQuestion = $response->answers->keyBy('question_id');

            $row = [
                (string) $response->id,
                $response->submitted_at?->toIso8601String() ?? '',
                $response->channel ?? 'web',
            ];

            foreach ($questions as $question) {
                $answer = $answersByQuestion->get($question->id);
                $row[] = $this->formatAnswer($answer?->answer_text, $answer?->answer_file_path);
            }

            fputcsv($handle, $row);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $csvContent === false ? '' : $csvContent;
    }

    private function formatAnswer(?string $answerText, ?string $filePath): string
    {
        if ($filePath) {
            return $filePath;
        }

        return $answerText ?? '';
    }
}
