<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SchemaAlignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('FormCreator', 'web');
        Role::findOrCreate('Respondent', 'web');
    }

    public function test_required_tables_exist_after_migrations(): void
    {
        $this->assertTrue(Schema::hasTable('survey_media'));
        $this->assertTrue(Schema::hasTable('accessibility_issues'));
        $this->assertTrue(Schema::hasTable('audit_logs'));
        $this->assertTrue(Schema::hasTable('exports'));
        $this->assertTrue(Schema::hasTable('response_channels'));
    }

    public function test_survey_media_relation_allows_media_creation(): void
    {
        $survey = Survey::factory()->create();

        $media = $survey->media()->create([
            'media_type' => 'image',
            'file_path' => 'surveys/demo.png',
            'alt_text' => 'Demo image',
            'position' => 1,
        ]);

        $this->assertInstanceOf(SurveyMedia::class, $media);
        $this->assertDatabaseHas('survey_media', [
            'id' => $media->id,
            'survey_id' => $survey->id,
            'media_type' => 'image',
        ]);
    }

    public function test_audit_log_created_when_creator_creates_survey(): void
    {
        $creator = User::factory()->create();
        $creator->assignRole('FormCreator');

        $response = $this->actingAs($creator)->post(route('creator.surveys.store'), [
            'title' => 'Audit Log Survey',
            'description' => 'Testing audit logging.',
            'status' => 'draft',
        ]);

        $survey = Survey::query()->where('creator_id', $creator->id)->first();

        $response->assertRedirect(route('creator.surveys.edit', $survey, false));
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created_survey',
            'entity_type' => 'Survey',
            'entity_id' => $survey->id,
        ]);
    }
}
