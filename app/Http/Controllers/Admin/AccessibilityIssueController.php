<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessibilityIssueController extends Controller
{
    /**
     * Display accessibility issues with filters.
     */
    public function index(Request $request): View
    {
        $issuesQuery = AccessibilityIssue::query()
            ->with(['survey.creator'])
            ->orderByDesc('detected_at');

        if ($request->filled('status')) {
            $issuesQuery->where('status', $request->input('status'));
        }

        if ($request->filled('severity')) {
            $issuesQuery->where('severity', $request->input('severity'));
        }

        if ($request->filled('issue_type')) {
            $issuesQuery->where('issue_type', $request->input('issue_type'));
        }

        if ($request->filled('survey_id')) {
            $issuesQuery->where('survey_id', (int) $request->input('survey_id'));
        }

        if ($request->filled('creator_id')) {
            $creatorId = (int) $request->input('creator_id');
            $issuesQuery->whereHas('survey', function ($query) use ($creatorId): void {
                $query->where('creator_id', $creatorId);
            });
        }

        $issues = $issuesQuery->paginate(20)->withQueryString();

        $issueTypes = AccessibilityIssue::query()
            ->select('issue_type')
            ->distinct()
            ->orderBy('issue_type')
            ->pluck('issue_type');

        return view('admin.accessibility.issues.index', [
            'pageTitle' => 'Accessibility Issues',
            'issues' => $issues,
            'issueTypes' => $issueTypes,
            'filters' => [
                'status' => $request->input('status'),
                'severity' => $request->input('severity'),
                'issue_type' => $request->input('issue_type'),
                'survey_id' => $request->input('survey_id'),
                'creator_id' => $request->input('creator_id'),
            ],
        ]);
    }

    /**
     * Update issue status.
     */
    public function update(Request $request, AccessibilityIssue $issue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,resolved,ignored'],
        ]);

        $status = $validated['status'];

        $issue->status = $status;
        $issue->resolved_at = $status === 'resolved' ? now() : null;
        $issue->save();

        return redirect()
            ->back()
            ->with('status', 'Accessibility issue updated.');
    }
}
