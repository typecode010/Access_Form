<?php

namespace Tests\Feature\Creator;

use App\Models\QuestionOption;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('FormCreator', 'web');
        Role::findOrCreate('Respondent', 'web');
    }

    public function test_form_creator_can_manage_own_surveys(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $storeResponse = $this->actingAs($creator)->post(route('creator.surveys.store'), [
            'title' => 'Accessibility Pilot',
            'description' => 'Survey for accessibility testing.',
            'status' => 'draft',
        ]);

        $survey = Survey::query()->where('creator_id', $creator->id)->first();

        $this->assertNotNull($survey);
        $storeResponse->assertRedirect(route('creator.surveys.edit', $survey, false));

        $updateResponse = $this->actingAs($creator)->put(route('creator.surveys.update', $survey), [
            'title' => 'Accessibility Pilot Updated',
            'description' => 'Updated description.',
            'status' => 'published',
        ]);

        $updateResponse->assertRedirect(route('creator.surveys.edit', $survey, false));
        $this->assertDatabaseHas('surveys', [
            'id' => $survey->id,
            'title' => 'Accessibility Pilot Updated',
            'status' => 'published',
        ]);

        $deleteResponse = $this->actingAs($creator)->delete(route('creator.surveys.destroy', $survey));
        $deleteResponse->assertRedirect(route('creator.surveys.index', [], false));

        $this->assertDatabaseMissing('surveys', [
            'id' => $survey->id,
        ]);
    }

    public function test_form_creator_cannot_preview_other_creators_survey(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('FormCreator');

        $other = User::factory()->create();
        $other->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->get(route('creator.surveys.preview', $survey))
            ->assertForbidden();
    }

    public function test_preview_shows_accessibility_warnings_for_missing_items(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $question = SurveyQuestion::factory()->multipleChoice()->create([
            'survey_id' => $survey->id,
            'position' => 1,
            'prompt' => 'Select a device',
        ]);

        QuestionOption::factory()->create([
            'survey_question_id' => $question->id,
            'position' => 1,
        ]);

        SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'type' => SurveyQuestion::TYPE_FILE,
            'position' => 2,
            'prompt' => 'Upload a file',
            'settings_json' => null,
        ]);

        $response = $this->actingAs($creator)->get(route('creator.surveys.preview', $survey));

        $response->assertOk();
        $response->assertSee('Question #1 needs at least 2 options.');
        $response->assertSee('Question #2 (file upload) is missing file constraints.');
    }
}
