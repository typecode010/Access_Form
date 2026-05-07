<?php

use App\Http\Controllers\Admin\AccessibilityIssueController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Creator\QuestionOptionController;
use App\Http\Controllers\Creator\DashboardController;
use App\Http\Controllers\Creator\SurveyAccessibilitySettingsController;
use App\Http\Controllers\Creator\SurveyAnalyticsController;
use App\Http\Controllers\Creator\SurveyExportController;
use App\Http\Controllers\Creator\SurveyMediaController;
use App\Http\Controllers\Creator\SurveyResponsesController;
use App\Http\Controllers\Creator\SurveyController;
use App\Http\Controllers\Creator\SurveyQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Respondent\DashboardController as RespondentDashboardController;
use App\Http\Controllers\Respondent\SubmissionController;
use App\Http\Controllers\Respondent\SurveyCatalogController;
use App\Http\Controllers\Respondent\SurveyResponseController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::get('/f/{slug}', [SurveyResponseController::class, 'show'])->name('surveys.public.show');
Route::post('/f/{slug}', [SurveyResponseController::class, 'submit'])->name('surveys.public.submit');
Route::get('/f/{slug}/thanks', [SurveyResponseController::class, 'thanks'])->name('surveys.public.thanks');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (! $user->hasAnyRole(['Admin', 'FormCreator', 'Respondent'])) {
        Role::findOrCreate('Respondent', 'web');
        $user->assignRole('Respondent');
    }

    if ($user->hasRole('Admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('FormCreator')) {
        return redirect()->route('creator.dashboard');
    }

    return redirect()->route('respondent.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role.update');

        Route::get('/accessibility/issues', [AccessibilityIssueController::class, 'index'])
            ->name('accessibility.issues.index');
        Route::put('/accessibility/issues/{issue}', [AccessibilityIssueController::class, 'update'])
            ->name('accessibility.issues.update');
    });

Route::middleware(['auth', 'role:FormCreator'])
    ->prefix('creator')
    ->name('creator.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/surveys', [SurveyController::class, 'index'])->name('surveys.index');
        Route::get('/surveys/create', [SurveyController::class, 'create'])->name('surveys.create');
        Route::post('/surveys', [SurveyController::class, 'store'])->name('surveys.store');
        Route::get('/surveys/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
        Route::get('/surveys/{survey}/preview', [SurveyController::class, 'preview'])->name('surveys.preview');
        Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])->name('surveys.edit');
        Route::put('/surveys/{survey}', [SurveyController::class, 'update'])->name('surveys.update');
        Route::delete('/surveys/{survey}', [SurveyController::class, 'destroy'])->name('surveys.destroy');

        Route::get('/surveys/{survey}/accessibility', [SurveyAccessibilitySettingsController::class, 'edit'])
            ->name('surveys.accessibility.edit');
        Route::put('/surveys/{survey}/accessibility', [SurveyAccessibilitySettingsController::class, 'update'])
            ->name('surveys.accessibility.update');

        Route::get('/surveys/{survey}/responses', [SurveyResponsesController::class, 'index'])
            ->name('surveys.responses.index');
        Route::get('/surveys/{survey}/analytics', [SurveyAnalyticsController::class, 'show'])
            ->name('surveys.analytics.show');
        Route::post('/surveys/{survey}/exports/csv', [SurveyExportController::class, 'storeCsv'])
            ->name('surveys.exports.csv');
        Route::get('/exports/{export}/download', [SurveyExportController::class, 'download'])
            ->name('exports.download');

        Route::get('/surveys/{survey}/media', [SurveyMediaController::class, 'index'])
            ->name('surveys.media.index');
        Route::post('/surveys/{survey}/media', [SurveyMediaController::class, 'store'])
            ->name('surveys.media.store');
        Route::get('/surveys/{survey}/media/{media}/edit', [SurveyMediaController::class, 'edit'])
            ->name('surveys.media.edit');
        Route::put('/surveys/{survey}/media/{media}', [SurveyMediaController::class, 'update'])
            ->name('surveys.media.update');
        Route::delete('/surveys/{survey}/media/{media}', [SurveyMediaController::class, 'destroy'])
            ->name('surveys.media.destroy');
        Route::post('/surveys/{survey}/media/reorder', [SurveyMediaController::class, 'reorder'])
            ->name('surveys.media.reorder');

        Route::get('/surveys/{survey}/questions', [SurveyQuestionController::class, 'index'])->name('surveys.questions.index');
        Route::get('/surveys/{survey}/questions/create', [SurveyQuestionController::class, 'create'])->name('surveys.questions.create');
        Route::post('/surveys/{survey}/questions', [SurveyQuestionController::class, 'store'])->name('surveys.questions.store');
        Route::post('/surveys/{survey}/questions/reorder', [SurveyQuestionController::class, 'reorder'])->name('surveys.questions.reorder');
        Route::get('/surveys/{survey}/questions/{question}/edit', [SurveyQuestionController::class, 'edit'])->name('surveys.questions.edit');
        Route::put('/surveys/{survey}/questions/{question}', [SurveyQuestionController::class, 'update'])->name('surveys.questions.update');
        Route::delete('/surveys/{survey}/questions/{question}', [SurveyQuestionController::class, 'destroy'])->name('surveys.questions.destroy');

        Route::post('/surveys/{survey}/questions/{question}/options', [QuestionOptionController::class, 'store'])->name('surveys.questions.options.store');
        Route::put('/surveys/{survey}/questions/{question}/options/{option}', [QuestionOptionController::class, 'update'])->name('surveys.questions.options.update');
        Route::delete('/surveys/{survey}/questions/{question}/options/{option}', [QuestionOptionController::class, 'destroy'])->name('surveys.questions.options.destroy');
    });

Route::middleware(['auth', 'role:Respondent'])
    ->prefix('respondent')
    ->name('respondent.')
    ->group(function () {
        Route::get('/dashboard', [RespondentDashboardController::class, 'index'])->name('dashboard');

        Route::get('/surveys', [SurveyCatalogController::class, 'index'])->name('surveys.index');
        Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    });

require __DIR__.'/auth.php';
