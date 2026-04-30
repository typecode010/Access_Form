<?php

namespace Database\Seeders;

use App\Models\QuestionOption;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Seed initial roles and one default user per role.
     */
    public function run(): void
    {
        $roles = ['Admin', 'FormCreator', 'Respondent'];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        $defaultUsers = [
            [
                'name' => 'AccessForm Admin',
                'email' => 'admin@accessform.local',
                'password' => env('SEED_ADMIN_PASSWORD', 'ChangeAdminPassword123!'),
                'role' => 'Admin',
            ],
            [
                'name' => 'AccessForm Form Creator',
                'email' => 'creator@accessform.local',
                'password' => env('SEED_FORMCREATOR_PASSWORD', 'ChangeFormCreatorPassword123!'),
                'role' => 'FormCreator',
            ],
            [
                'name' => 'AccessForm Respondent',
                'email' => 'respondent@accessform.local',
                'password' => env('SEED_RESPONDENT_PASSWORD', 'ChangeRespondentPassword123!'),
                'role' => 'Respondent',
            ],
        ];

        foreach ($defaultUsers as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$account['role']]);
        }

        $this->seedCreatorSurveyDemo();
    }

    /**
     * Seed one demo survey with starter questions for the default creator.
     */
    private function seedCreatorSurveyDemo(): void
    {
        if (! Schema::hasTable('surveys') || ! Schema::hasTable('survey_questions') || ! Schema::hasTable('question_options')) {
            return;
        }

        $creator = User::query()->where('email', 'creator@accessform.local')->first();

        if (! $creator) {
            return;
        }

        $survey = Survey::updateOrCreate(
            [
                'creator_id' => $creator->id,
                'public_slug' => 'accessibility-demo-survey',
            ],
            [
                'title' => 'Accessibility Demo Survey',
                'description' => 'Starter survey seeded for creator question builder demos.',
                'status' => 'draft',
            ]
        );

        $supportsSettingsJson = Schema::hasColumn('survey_questions', 'settings_json');
        $optionForeignKey = Schema::hasColumn('question_options', 'survey_question_id')
            ? 'survey_question_id'
            : 'question_id';

        $textQuestionPayload = [
            'type' => SurveyQuestion::TYPE_TEXT,
            'prompt' => 'What accessibility feature helps you the most?',
            'help_text' => 'Example: high contrast mode, keyboard navigation, or captions.',
            'is_required' => true,
        ];

        if ($supportsSettingsJson) {
            $textQuestionPayload['settings_json'] = null;
        }

        SurveyQuestion::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'position' => 1,
            ],
            $textQuestionPayload
        );

        $multipleChoicePayload = [
            'type' => SurveyQuestion::TYPE_MULTIPLE_CHOICE,
            'prompt' => 'Which device do you use most for online forms?',
            'help_text' => null,
            'is_required' => true,
        ];

        if ($supportsSettingsJson) {
            $multipleChoicePayload['settings_json'] = null;
        }

        $multipleChoiceQuestion = SurveyQuestion::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'position' => 2,
            ],
            $multipleChoicePayload
        );

        $ratingPayload = [
            'type' => SurveyQuestion::TYPE_RATING,
            'prompt' => 'Rate the clarity of this form.',
            'help_text' => '1 means very unclear and 5 means very clear.',
            'is_required' => true,
        ];

        if ($supportsSettingsJson) {
            $ratingPayload['settings_json'] = [
                'min' => 1,
                'max' => 5,
            ];
        }

        SurveyQuestion::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'position' => 3,
            ],
            $ratingPayload
        );

        $options = [
            ['position' => 1, 'option_text' => 'Desktop/Laptop', 'option_value' => 'desktop'],
            ['position' => 2, 'option_text' => 'Mobile Phone', 'option_value' => 'mobile'],
            ['position' => 3, 'option_text' => 'Tablet', 'option_value' => 'tablet'],
        ];

        foreach ($options as $option) {
            QuestionOption::updateOrCreate(
                [
                    $optionForeignKey => $multipleChoiceQuestion->id,
                    'position' => $option['position'],
                ],
                [
                    'option_text' => $option['option_text'],
                    'option_value' => $option['option_value'],
                ]
            );
        }
    }
}
