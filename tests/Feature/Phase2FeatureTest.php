<?php

namespace Tests\Feature;

use App\Models\AccessibilityIssue;
use App\Models\Export;
use App\Models\QuestionOption;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Models\Survey;
use App\Models\SurveyMedia;
use App\Models\SurveyQuestion;
use App\Models\User;
use App\Services\AccessibilityIssueDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase2FeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('FormCreator', 'web');
        Role::findOrCreate('Respondent', 'web');
    }

    public function test_creator_can_add_media_and_preview_shows_it(): void
    {
        Storage::fake('public');

        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $response = $this->actingAs($creator)->post(route('creator.surveys.media.store', $survey), [
            'media_type' => 'image',
            'media_file' => UploadedFile::fake()->create('example.jpg', 120, 'image/jpeg'),
            'alt_text' => 'Sample alt text',
        ]);

        $response->assertRedirect(route('creator.surveys.media.index', $survey));

        $media = SurveyMedia::query()->where('survey_id', $survey->id)->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->file_path);

        $preview = $this->actingAs($creator)->get(route('creator.surveys.preview', $survey));
        $preview->assertOk();
        $preview->assertSee('Alt text: Sample alt text');
    }

    public function test_detector_creates_issues_for_missing_alt_and_mcq_options(): void
    {
        $survey = Survey::factory()->create();

        $question = SurveyQuestion::factory()->multipleChoice()->create([
            'survey_id' => $survey->id,
            'position' => 1,
            'prompt' => 'Pick one',
        ]);

        QuestionOption::factory()->create([
            'survey_question_id' => $question->id,
            'position' => 1,
        ]);

        SurveyMedia::create([
            'survey_id' => $survey->id,
            'media_type' => 'image',
            'file_path' => 'surveys/demo.png',
            'alt_text' => null,
            'position' => 1,
        ]);

        app(AccessibilityIssueDetector::class)->detect($survey, true);

        $this->assertDatabaseHas('accessibility_issues', [
            'survey_id' => $survey->id,
            'issue_type' => AccessibilityIssueDetector::ISSUE_IMAGE_MISSING_ALT,
        ]);

        $this->assertDatabaseHas('accessibility_issues', [
            'survey_id' => $survey->id,
            'issue_type' => AccessibilityIssueDetector::ISSUE_MCQ_INSUFFICIENT_OPTIONS,
        ]);
    }

    public function test_admin_can_view_and_update_accessibility_issues(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $survey = Survey::factory()->create();

        $issue = AccessibilityIssue::create([
            'survey_id' => $survey->id,
            'issue_type' => AccessibilityIssueDetector::ISSUE_MISSING_TITLE,
            'severity' => 'error',
            'status' => 'open',
            'message' => 'Survey title is missing.',
        ]);

        $index = $this->actingAs($admin)->get(route('admin.accessibility.issues.index'));
        $index->assertOk();
        $index->assertSee($issue->message);

        $update = $this->actingAs($admin)->put(route('admin.accessibility.issues.update', $issue), [
            'status' => 'resolved',
        ]);

        $update->assertRedirect();

        $issue->refresh();
        $this->assertSame('resolved', $issue->status);
        $this->assertNotNull($issue->resolved_at);
    }

    public function test_creator_can_generate_csv_export(): void
    {
        Storage::fake('public');

        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $question = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'position' => 1,
        ]);

        $response = Response::create([
            'survey_id' => $survey->id,
            'respondent_id' => null,
            'channel' => 'web',
            'submitted_at' => now(),
        ]);

        ResponseAnswer::create([
            'response_id' => $response->id,
            'question_id' => $question->id,
            'answer_text' => 'Example answer',
        ]);

        $request = $this->actingAs($creator)->post(route('creator.surveys.exports.csv', $survey));
        $request->assertRedirect(route('creator.surveys.analytics.show', $survey));

        $export = Export::query()->where('survey_id', $survey->id)->first();
        $this->assertNotNull($export);
        Storage::disk('public')->assertExists($export->file_path);
    }
}
