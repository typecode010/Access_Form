<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\View\View;

class SurveyCatalogController extends Controller
{
    public function index(): View
    {
        $surveys = Survey::query()
            ->where('status', 'published')
            ->with(['creator', 'accessibilitySettings'])
            ->withCount('questions')
            ->latest('updated_at')
            ->paginate(12);

        return view('respondent.surveys.index', [
            'pageTitle' => 'Available Surveys',
            'roleName' => 'Respondent',
            'surveys' => $surveys,
        ]);
    }
}
