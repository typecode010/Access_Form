<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\View\View;

class SurveyResponsesController extends Controller
{
    public function index(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        $responses = Response::query()
            ->where('survey_id', $survey->id)
            ->with(['respondent:id,name,email'])
            ->withCount('answers')
            ->latest('submitted_at')
            ->paginate(15);

        $totalResponses = Response::query()
            ->where('survey_id', $survey->id)
            ->count();

        $latestResponse = Response::query()
            ->where('survey_id', $survey->id)
            ->latest('submitted_at')
            ->first();

        return view('creator.surveys.responses.index', [
            'pageTitle' => 'Survey Responses',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'responses' => $responses,
            'totalResponses' => $totalResponses,
            'latestResponse' => $latestResponse,
        ]);
    }

    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }
}
