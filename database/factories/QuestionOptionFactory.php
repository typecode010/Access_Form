<?php

namespace Database\Factories;

use App\Models\QuestionOption;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    protected $model = QuestionOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'survey_question_id' => SurveyQuestion::factory(),
            'option_text' => fake()->words(3, true),
            'option_value' => null,
            'position' => 1,
        ];
    }
}
