<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyQuestion>
 */
class SurveyQuestionFactory extends Factory
{
    protected $model = SurveyQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'survey_id' => Survey::factory(),
            'type' => SurveyQuestion::TYPE_TEXT,
            'prompt' => fake()->sentence(6),
            'help_text' => fake()->optional()->sentence(),
            'is_required' => false,
            'position' => 1,
            'settings_json' => null,
        ];
    }

    public function multipleChoice(): static
    {
        return $this->state(fn () => [
            'type' => SurveyQuestion::TYPE_MULTIPLE_CHOICE,
        ]);
    }
}
