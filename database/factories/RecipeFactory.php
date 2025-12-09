<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(), 
            'name'         => $this->faker->words(3, true),
            'serving_size' => $this->faker->randomElement([
                '1 plate', '100 g', '1 bowl', '2 slices',
            ]),
            'calories'     => $this->faker->numberBetween(200, 800),
            'protein'      => $this->faker->randomFloat(1, 5, 40),
            'carbs'        => $this->faker->randomFloat(1, 10, 100),
            'fat'          => $this->faker->randomFloat(1, 2, 40),
            'fiber'        => $this->faker->randomFloat(1, 1, 15),
            'description'  => $this->faker->sentence(8),
        ];
    }
}
