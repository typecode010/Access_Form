<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityIssue;
use App\Models\AuditLog;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tables = [
            'responses' => Schema::hasTable('responses'),
            'accessibility_issues' => Schema::hasTable('accessibility_issues'),
            'audit_logs' => Schema::hasTable('audit_logs'),
        ];

        $metrics = [
            'users' => User::count(),
            'surveys' => Survey::count(),
            'published_surveys' => Survey::query()->where('status', 'published')->count(),
            'responses' => $tables['responses'] ? Response::count() : null,
            'open_issues' => $tables['accessibility_issues']
                ? AccessibilityIssue::query()->where('status', 'open')->count()
                : null,
        ];

        $severityCounts = collect();
        $topIssueTypes = collect();

        if ($tables['accessibility_issues']) {
            $severityCounts = AccessibilityIssue::query()
                ->where('status', 'open')
                ->select('severity', DB::raw('count(*) as total'))
                ->groupBy('severity')
                ->pluck('total', 'severity');

            $topIssueTypes = AccessibilityIssue::query()
                ->where('status', 'open')
                ->select('issue_type', DB::raw('count(*) as total'))
                ->groupBy('issue_type')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        $recentAuditLogs = $tables['audit_logs']
            ? AuditLog::query()->with('actor:id,name,email')->latest()->limit(10)->get()
            : collect();

        return view('admin.dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'roleName' => 'Admin',
            'tables' => $tables,
            'metrics' => $metrics,
            'severityCounts' => $severityCounts,
            'topIssueTypes' => $topIssueTypes,
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }
}
