<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\ReorderSurveyMediaRequest;
use App\Http\Requests\Creator\StoreSurveyMediaRequest;
use App\Http\Requests\Creator\UpdateSurveyMediaRequest;
use App\Models\Survey;
use App\Models\SurveyMedia;
use App\Services\AccessibilityIssueDetector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SurveyMediaController extends Controller
{
    /**
     * Display media items for a survey.
     */
    public function index(Survey $survey): View
    {
        $this->ensureSurveyOwnership($survey);

        $mediaItems = $survey->media()->get();

        return view('creator.surveys.media.index', [
            'pageTitle' => 'Survey Media',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'mediaItems' => $mediaItems,
        ]);
    }

    /**
     * Store a new media item.
     */
    public function store(StoreSurveyMediaRequest $request, Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);
        $validated = $request->validated();

        $mediaPath = $this->storeMediaFile($request, $survey);
        $captionPath = $this->storeCaptionFile($request, $survey);

        DB::transaction(function () use ($survey, $validated, $mediaPath, $captionPath): void {
            $targetPosition = $this->resolveCreatePosition($survey, $validated['position'] ?? null);
            $this->shiftMediaDownFrom($survey, $targetPosition);

            $survey->media()->create([
                'media_type' => $validated['media_type'],
                'file_path' => $mediaPath,
                'alt_text' => $validated['alt_text'] ?? null,
                'caption_path' => $captionPath,
                'transcript_text' => $validated['transcript_text'] ?? null,
                'sign_language_video_url' => $validated['sign_language_video_url'] ?? null,
                'position' => $targetPosition,
            ]);
        });

        $this->refreshAccessibilityIssues($survey);

        return redirect()
            ->route('creator.surveys.media.index', $survey)
            ->with('status', 'Media item added successfully.');
    }

    /**
     * Show edit form for a media item.
     */
    public function edit(Survey $survey, SurveyMedia $media): View
    {
        $this->ensureMediaAccess($survey, $media);

        return view('creator.surveys.media.edit', [
            'pageTitle' => 'Edit Media',
            'roleName' => 'FormCreator',
            'survey' => $survey,
            'media' => $media,
        ]);
    }

    /**
     * Update a media item.
     */
    public function update(UpdateSurveyMediaRequest $request, Survey $survey, SurveyMedia $media): RedirectResponse
    {
        $this->ensureMediaAccess($survey, $media);
        $validated = $request->validated();

        DB::transaction(function () use ($survey, $media, $validated, $request): void {
            $media->fill([
                'media_type' => $validated['media_type'],
                'alt_text' => $validated['alt_text'] ?? null,
                'transcript_text' => $validated['transcript_text'] ?? null,
                'sign_language_video_url' => $validated['sign_language_video_url'] ?? null,
            ]);

            if ($request->hasFile('media_file')) {
                $this->deleteFile($media->file_path);
                $media->file_path = $this->storeUploadedFile($request->file('media_file'), $survey, 'media');
            }

            if ($request->hasFile('caption_file')) {
                $this->deleteFile($media->caption_path);
                $media->caption_path = $this->storeUploadedFile($request->file('caption_file'), $survey, 'captions');
            }

            $targetPosition = $this->resolveUpdatePosition(
                $survey,
                $media,
                $validated['position'] ?? $media->position
            );

            if ($targetPosition !== (int) $media->position) {
                $this->moveMediaToPosition($survey, $media, $targetPosition);
            }

            $media->position = $targetPosition;
            $media->save();
        });

        $this->refreshAccessibilityIssues($survey);

        return redirect()
            ->route('creator.surveys.media.edit', [$survey, $media])
            ->with('status', 'Media item updated successfully.');
    }

    /**
     * Delete a media item.
     */
    public function destroy(Survey $survey, SurveyMedia $media): RedirectResponse
    {
        $this->ensureMediaAccess($survey, $media);

        DB::transaction(function () use ($survey, $media): void {
            $this->deleteFile($media->file_path);
            $this->deleteFile($media->caption_path);
            $media->delete();
            $this->normalizeMediaPositions($survey);
        });

        $this->refreshAccessibilityIssues($survey);

        return redirect()
            ->route('creator.surveys.media.index', $survey)
            ->with('status', 'Media item deleted successfully.');
    }

    /**
     * Reorder media items.
     */
    public function reorder(ReorderSurveyMediaRequest $request, Survey $survey): RedirectResponse
    {
        $this->ensureSurveyOwnership($survey);

        $orderedIds = collect($request->validated()['ordered_ids'])
            ->map(fn ($id) => (int) $id)
            ->values();

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $mediaId) {
                SurveyMedia::query()
                    ->whereKey($mediaId)
                    ->update(['position' => $index + 1]);
            }
        });

        $this->refreshAccessibilityIssues($survey);

        return redirect()
            ->route('creator.surveys.media.index', $survey)
            ->with('status', 'Media order updated successfully.');
    }

    /**
     * Ensure the authenticated creator owns the survey.
     */
    private function ensureSurveyOwnership(Survey $survey): void
    {
        abort_unless((int) $survey->creator_id === (int) auth()->id(), 403, 'You are not allowed to access this survey.');
    }

    /**
     * Ensure survey ownership and media belongs to survey.
     */
    private function ensureMediaAccess(Survey $survey, SurveyMedia $media): void
    {
        $this->ensureSurveyOwnership($survey);
        abort_unless((int) $media->survey_id === (int) $survey->id, 403, 'You are not allowed to access this media item.');
    }

    private function resolveCreatePosition(Survey $survey, ?int $requestedPosition): int
    {
        $maxPosition = (int) $survey->media()->max('position');

        if ($requestedPosition === null) {
            return $maxPosition + 1;
        }

        return max(1, min($requestedPosition, $maxPosition + 1));
    }

    private function resolveUpdatePosition(Survey $survey, SurveyMedia $media, int $requestedPosition): int
    {
        $maxPosition = (int) $survey->media()->max('position');
        $maxPosition = max(1, $maxPosition);

        return max(1, min($requestedPosition, $maxPosition));
    }

    private function shiftMediaDownFrom(Survey $survey, int $fromPosition): void
    {
        SurveyMedia::query()
            ->where('survey_id', $survey->id)
            ->where('position', '>=', $fromPosition)
            ->increment('position');
    }

    private function moveMediaToPosition(Survey $survey, SurveyMedia $media, int $targetPosition): void
    {
        $currentPosition = (int) $media->position;

        if ($targetPosition < $currentPosition) {
            SurveyMedia::query()
                ->where('survey_id', $survey->id)
                ->whereBetween('position', [$targetPosition, $currentPosition - 1])
                ->increment('position');

            return;
        }

        SurveyMedia::query()
            ->where('survey_id', $survey->id)
            ->whereBetween('position', [$currentPosition + 1, $targetPosition])
            ->decrement('position');
    }

    private function normalizeMediaPositions(Survey $survey): void
    {
        $survey->media()
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->each(function (SurveyMedia $media, int $index): void {
                $newPosition = $index + 1;

                if ((int) $media->position !== $newPosition) {
                    $media->update(['position' => $newPosition]);
                }
            });
    }

    private function storeMediaFile(StoreSurveyMediaRequest $request, Survey $survey): string
    {
        $file = $request->file('media_file');

        return $this->storeUploadedFile($file, $survey, 'media');
    }

    private function storeCaptionFile(StoreSurveyMediaRequest $request, Survey $survey): ?string
    {
        $file = $request->file('caption_file');

        if (! $file) {
            return null;
        }

        return $this->storeUploadedFile($file, $survey, 'captions');
    }

    private function storeUploadedFile(UploadedFile $file, Survey $survey, string $folder): string
    {
        return $file->store('surveys/'.$survey->id.'/'.$folder, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function refreshAccessibilityIssues(Survey $survey): void
    {
        app(AccessibilityIssueDetector::class)->detect($survey, true);
    }
}
