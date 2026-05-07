<?php

namespace Tests\Feature\Creator;

use App\Models\Survey;
use App\Models\SurveyAccessibilitySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyAccessibilitySettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('FormCreator', 'web');
        Role::findOrCreate('Respondent', 'web');
    }

    public function test_default_settings_created_when_survey_is_created(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $this->assertDatabaseHas('survey_accessibility_settings', [
            'survey_id' => $survey->id,
            'keyboard_nav_enforced' => 1,
        ]);
    }

    public function test_creator_can_view_and_update_own_accessibility_settings(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        $this->actingAs($creator)
            ->get(route('creator.surveys.accessibility.edit', $survey))
            ->assertOk()
            ->assertSee('Accessibility Settings');

        $updateResponse = $this->actingAs($creator)->put(route('creator.surveys.accessibility.update', $survey), [
            'high_contrast_enabled' => '1',
            'dyslexia_friendly_enabled' => '1',
            'keyboard_nav_enforced' => '1',
            'text_size' => 'lg',
            'reduced_motion' => '1',
        ]);

        $updateResponse->assertRedirect(route('creator.surveys.accessibility.edit', $survey, false));

        $this->assertDatabaseHas('survey_accessibility_settings', [
            'survey_id' => $survey->id,
            'high_contrast_enabled' => 1,
            'dyslexia_friendly_enabled' => 1,
            'keyboard_nav_enforced' => 1,
            'text_size' => 'lg',
            'reduced_motion' => 1,
        ]);
    }

    public function test_creator_cannot_update_other_creators_settings(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('FormCreator');

        $other = User::factory()->create();
        $other->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->put(route('creator.surveys.accessibility.update', $survey), [
                'high_contrast_enabled' => '1',
                'dyslexia_friendly_enabled' => '1',
                'keyboard_nav_enforced' => '1',
                'text_size' => 'lg',
                'reduced_motion' => '1',
            ])
            ->assertForbidden();
    }

    public function test_preview_applies_accessibility_theme_classes(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $survey = Survey::factory()->create([
            'creator_id' => $creator->id,
        ]);

        SurveyAccessibilitySetting::query()
            ->where('survey_id', $survey->id)
            ->update([
                'high_contrast_enabled' => true,
                'dyslexia_friendly_enabled' => true,
                'keyboard_nav_enforced' => true,
                'text_size' => 'lg',
                'reduced_motion' => true,
            ]);

        $response = $this->actingAs($creator)->get(route('creator.surveys.preview', $survey));

        $response->assertOk();
        $response->assertSee('preview-theme theme-contrast theme-dyslexia reduced-motion text-size-lg');
    }
}
