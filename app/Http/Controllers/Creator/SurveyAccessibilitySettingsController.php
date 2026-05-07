<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\UpdateSurveyAccessibilitySettingsRequest;
use App\Models\Survey;
use App\Models\SurveyAccessibilitySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SurveyAccessibilitySettingsController extends Controller
{
    /**
     * Show the accessibility settings form for a survey.
     */
    public function edit(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        $settings = $survey->accessibilitySettings()
            ->firstOrCreate([], SurveyAccessibilitySetting::defaults());

        return view('creator.surveys.accessibility', [
            'pageTitle' => 'Accessibility Settings',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'settings' => $settings,
        ]);
    }

    /**
     * Update accessibility settings for a survey.
     */
    public function update(UpdateSurveyAccessibilitySettingsRequest $request, Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);

        $settings = $survey->accessibilitySettings()
            ->firstOrCreate([], SurveyAccessibilitySetting::defaults());

        $settings->fill($request->validated());
        $settings->save();

        return redirect()
            ->route('creator.surveys.accessibility.edit', $survey)
            ->with('status', 'Accessibility settings updated.');
    }

    /**
     * Ensure the authenticated creator owns the survey.
     */
    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }
}
