<?php

namespace Tests\Feature\Creator;

use App\Models\QuestionOption;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyQuestionBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('FormCreator', 'web');
        Role::findOrCreate('Respondent', 'web');
    }

    public function test_form_creator_can_crud_own_survey_questions(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $storeResponse = $this->actingAs($creator)->post(route('creator.surveys.questions.store', $survey), [
            'type' => SurveyQuestion::TYPE_TEXT,
            'prompt' => 'What support tool do you use?',
            'help_text' => 'Share one tool you rely on.',
            'is_required' => '1',
            'position' => 1,
            'settings_json' => null,
        ]);

        $question = SurveyQuestion::query()->where('survey_id', $survey->id)->first();

        $storeResponse->assertRedirect(route('creator.surveys.questions.edit', [$survey, $question], false));
        $this->assertNotNull($question);
        $this->assertSame('What support tool do you use?', $question->prompt);

        $updateResponse = $this->actingAs($creator)->put(route('creator.surveys.questions.update', [$survey, $question]), [
            'type' => SurveyQuestion::TYPE_TEXT,
            'prompt' => 'Updated prompt text',
            'help_text' => 'Updated help',
            'is_required' => '0',
            'position' => 1,
            'settings_json' => null,
        ]);

        $updateResponse->assertRedirect(route('creator.surveys.questions.edit', [$survey, $question], false));
        $this->assertDatabaseHas('survey_questions', [
            'id' => $question->id,
            'prompt' => 'Updated prompt text',
        ]);

        $deleteResponse = $this->actingAs($creator)->delete(route('creator.surveys.questions.destroy', [$survey, $question]));
        $deleteResponse->assertRedirect(route('creator.surveys.questions.index', $survey, false));

        $this->assertDatabaseMissing('survey_questions', [
            'id' => $question->id,
        ]);
    }

    public function test_form_creator_cannot_manage_another_creators_survey_questions(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('FormCreator');

        $otherCreator = User::factory()->create();
        $otherCreator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $owner->id,
        ]);

        $question = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'position' => 1,
        ]);

        $this->actingAs($otherCreator)
            ->get(route('creator.surveys.questions.index', $survey))
            ->assertForbidden();

        $this->actingAs($otherCreator)
            ->post(route('creator.surveys.questions.store', $survey), [
                'type' => SurveyQuestion::TYPE_TEXT,
                'prompt' => 'Should fail',
                'is_required' => '0',
                'settings_json' => null,
            ])
            ->assertForbidden();

        $this->actingAs($otherCreator)
            ->put(route('creator.surveys.questions.update', [$survey, $question]), [
                'type' => SurveyQuestion::TYPE_TEXT,
                'prompt' => 'Unauthorized update',
                'is_required' => '0',
                'position' => 1,
                'settings_json' => null,
            ])
            ->assertForbidden();
    }

    public function test_admin_cannot_access_creator_question_builder_routes(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $this->actingAs($admin)
            ->get(route('creator.surveys.questions.index', $survey))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('creator.surveys.questions.store', $survey), [
                'type' => SurveyQuestion::TYPE_TEXT,
                'prompt' => 'Admin should not create this',
                'is_required' => '0',
                'settings_json' => null,
            ])
            ->assertForbidden();
    }

    public function test_reorder_endpoint_updates_positions_correctly(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $q1 = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'position' => 1,
            'prompt' => 'Q1',
        ]);
        $q2 = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'position' => 2,
            'prompt' => 'Q2',
        ]);
        $q3 = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'position' => 3,
            'prompt' => 'Q3',
        ]);

        $response = $this->actingAs($creator)->post(route('creator.surveys.questions.reorder', $survey), [
            'ordered_ids' => [$q3->id, $q1->id, $q2->id],
        ]);

        $response->assertRedirect(route('creator.surveys.questions.index', $survey, false));

        $this->assertDatabaseHas('survey_questions', ['id' => $q3->id, 'position' => 1]);
        $this->assertDatabaseHas('survey_questions', ['id' => $q1->id, 'position' => 2]);
        $this->assertDatabaseHas('survey_questions', ['id' => $q2->id, 'position' => 3]);
    }

    public function test_option_crud_works_for_multiple_choice_questions(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $question = SurveyQuestion::factory()->multipleChoice()->create([
            'survey_id' => $survey->id,
            'position' => 1,
        ]);

        $storeResponse = $this->actingAs($creator)->post(route('creator.surveys.questions.options.store', [$survey, $question]), [
            'option_text' => 'Option One',
            'option_value' => 'one',
            'position' => 1,
        ]);

        $storeResponse->assertRedirect(route('creator.surveys.questions.edit', [$survey, $question], false));

        $firstOption = QuestionOption::query()->where('survey_question_id', $question->id)->where('position', 1)->first();
        $this->assertNotNull($firstOption);

        QuestionOption::factory()->create([
            'survey_question_id' => $question->id,
            'position' => 2,
            'option_text' => 'Option Two',
        ]);
        QuestionOption::factory()->create([
            'survey_question_id' => $question->id,
            'position' => 3,
            'option_text' => 'Option Three',
        ]);

        $updateResponse = $this->actingAs($creator)->put(route('creator.surveys.questions.options.update', [$survey, $question, $firstOption]), [
            'option_text' => 'Option One Updated',
            'option_value' => 'one-updated',
            'position' => 2,
        ]);

        $updateResponse->assertRedirect(route('creator.surveys.questions.edit', [$survey, $question], false));

        $firstOption->refresh();
        $this->assertSame('Option One Updated', $firstOption->option_text);
        $this->assertSame(2, (int) $firstOption->position);

        $deleteResponse = $this->actingAs($creator)->delete(route('creator.surveys.questions.options.destroy', [$survey, $question, $firstOption]));
        $deleteResponse->assertRedirect(route('creator.surveys.questions.edit', [$survey, $question], false));

        $this->assertDatabaseMissing('question_options', ['id' => $firstOption->id]);
    }
}
