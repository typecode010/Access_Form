<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'creator_id' => User::factory(),
            'title' => $title,
            'description' => fake()->optional()->paragraph(),
            'status' => 'draft',
            'public_slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
        ];
    }
}
