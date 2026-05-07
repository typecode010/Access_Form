<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Response;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(): View
    {
        $respondentId = auth()->id();

        $responsesQuery = Response::query()
            ->where('respondent_id', $respondentId);

        $responses = (clone $responsesQuery)
            ->with(['survey:id,title,public_slug'])
            ->withCount('answers')
            ->latest('submitted_at')
            ->paginate(12);

        $latestSubmission = (clone $responsesQuery)
            ->with(['survey:id,title,public_slug'])
            ->latest('submitted_at')
            ->first();

        return view('respondent.submissions.index', [
            'pageTitle' => 'My Submissions',
            'roleName' => 'Respondent',
            'responses' => $responses,
            'latestSubmission' => $latestSubmission,
        ]);
    }
}
