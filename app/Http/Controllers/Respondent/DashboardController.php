<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $surveys = Survey::query()
            ->where('status', 'published')
            ->with(['creator', 'accessibilitySettings'])
            ->withCount('questions')
            ->latest('updated_at')
            ->paginate(9);

        $recentResponses = Response::query()
            ->where('respondent_id', auth()->id())
            ->with(['survey:id,title,public_slug'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $recentActivity = $recentResponses->map(function (Response $response): array {
            $submittedAt = $response->submitted_at ?? $response->created_at;

            return [
                'title' => $response->survey?->title ?? 'Survey',
                'status' => $response->submitted_at ? 'Submitted' : 'In progress',
                'time' => $submittedAt?->diffForHumans(),
                'slug' => $response->survey?->public_slug,
            ];
        });

        return view('respondent.dashboard', [
            'pageTitle' => 'Respondent Dashboard',
            'roleName' => 'Respondent',
            'surveys' => $surveys,
            'recentActivity' => $recentActivity,
        ]);
    }
}
