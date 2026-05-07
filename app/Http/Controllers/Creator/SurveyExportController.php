<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Export;
use App\Models\Survey;
use App\Services\SurveyCsvExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SurveyExportController extends Controller
{
    /**
     * Generate a CSV export for the survey.
     */
    public function storeCsv(Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);

        $export = app(SurveyCsvExportService::class)->generate($survey, auth()->user());

        return redirect()
            ->route('creator.surveys.analytics.show', $survey)
            ->with('status', "CSV export created (#{$export->id}).");
    }

    /**
     * Download an export file.
     */
    public function download(Export $export): BinaryFileResponse
    {
        $survey = $export->survey;

        abort_unless($survey && (int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this export.');

        if (! Storage::disk('public')->exists($export->file_path)) {
            abort(404, 'Export file not found.');
        }

        return Storage::disk('public')->download($export->file_path, basename($export->file_path));
    }

    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }
}
