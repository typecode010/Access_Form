<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Creator\QuestionOptionController;
use App\Http\Controllers\Creator\SurveyController;
use App\Http\Controllers\Creator\SurveyQuestionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

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
        Route::get('/dashboard', function () {
            return view('admin.dashboard', [
                'pageTitle' => 'Admin Dashboard',
                'roleName' => 'Admin',
            ]);
        })->name('dashboard');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role.update');
    });

Route::middleware(['auth', 'role:FormCreator'])
    ->prefix('creator')
    ->name('creator.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('creator.dashboard', [
                'pageTitle' => 'Form Creator Dashboard',
                'roleName' => 'FormCreator',
            ]);
        })->name('dashboard');

        Route::get('/surveys', [SurveyController::class, 'index'])->name('surveys.index');
        Route::get('/surveys/create', [SurveyController::class, 'create'])->name('surveys.create');
        Route::post('/surveys', [SurveyController::class, 'store'])->name('surveys.store');
        Route::get('/surveys/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
        Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])->name('surveys.edit');
        Route::put('/surveys/{survey}', [SurveyController::class, 'update'])->name('surveys.update');
        Route::delete('/surveys/{survey}', [SurveyController::class, 'destroy'])->name('surveys.destroy');

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
        Route::get('/dashboard', function () {
            return view('respondent.dashboard', [
                'pageTitle' => 'Respondent Dashboard',
                'roleName' => 'Respondent',
            ]);
        })->name('dashboard');

        Route::get('/surveys', function () {
            return view('respondent.surveys.index', [
                'pageTitle' => 'Respondent Surveys',
                'roleName' => 'Respondent',
            ]);
        })->name('surveys.index');
    });

require __DIR__.'/auth.php';
