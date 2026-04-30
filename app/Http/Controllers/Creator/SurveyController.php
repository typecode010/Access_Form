<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SurveyController extends Controller
{
    /**
     * Display a list of surveys created by the logged in creator.
     */
    public function index(): View
    {
        $surveys = Survey::query()
            ->where('creator_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('creator.surveys.index', [
            'pageTitle' => 'Creator Surveys',
            'roleName' => 'FormCreator',
            'surveys' => $surveys,
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('creator.surveys.create', [
            'pageTitle' => 'Create Survey',
            'roleName' => 'FormCreator',
            'survey' => new Survey(['status' => 'draft']),
        ]);
    }

    /**
     * Store a newly created survey.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $survey = Survey::create([
            'creator_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'public_slug' => $this->generateUniqueSlug($validated['title']),
        ]);

        return redirect()
            ->route('creator.surveys.edit', $survey)
            ->with('status', 'Survey created successfully.');
    }

    /**
     * Display a single survey.
     */
    public function show(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        return view('creator.surveys.show', [
            'pageTitle' => 'Survey Details',
            'roleName' => 'FormCreator',
            'survey' => $survey,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        return view('creator.surveys.edit', [
            'pageTitle' => 'Edit Survey',
            'roleName' => 'FormCreator',
            'survey' => $survey,
        ]);
    }

    /**
     * Update a survey.
     */
    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $titleChanged = $survey->title !== $validated['title'];

        $survey->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($titleChanged) {
            $survey->public_slug = $this->generateUniqueSlug($validated['title'], $survey->id);
        }

        $survey->save();

        return redirect()
            ->route('creator.surveys.edit', $survey)
            ->with('status', 'Survey updated successfully.');
    }

    /**
     * Delete a survey.
     */
    public function destroy(Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);
        $survey->delete();

        return redirect()
            ->route('creator.surveys.index')
            ->with('status', 'Survey deleted successfully.');
    }

    /**
     * Ensure the current creator owns the survey.
     */
    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }

    /**
     * Generate a unique public slug for a survey.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'survey';

        $slug = $base;
        $counter = 1;

        while (
            Survey::query()
                ->where('public_slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
