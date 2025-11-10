<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create/Update a default test user (Use updateOrCreate to ensure password is freshly hashed)
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Ensure the recipes table exists, then create some basic recipes assigned to the Test User
        if (Schema::hasTable('recipes')) {
            $recipes = [
                [
                    'name' => 'Grilled Chicken Salad',
                    'serving_size' => '1 Salad Portion',
                    'calories' => 350,
                    'protein' => 45.0,
                    'carbs' => 10.0,
                    'fat' => 15.0,
                    'description' => 'A classic low-fat, high-protein lunch option.',
                ],
                [
                    'name' => 'Scrambled Eggs (3 eggs)',
                    'serving_size' => '3 Eggs',
                    'calories' => 240,
                    'protein' => 18.0,
                    'carbs' => 2.0,
                    'fat' => 18.0,
                    'description' => 'Simple and quick breakfast.',
                ],
                [
                    'name' => 'Brown Rice',
                    'serving_size' => '100g Cooked Rice',
                    'calories' => 120,
                    'protein' => 2.6,
                    'carbs' => 25.0,
                    'fat' => 0.9,
                    'description' => 'A healthy source of complex carbohydrates.',
                ],
            ];

            foreach ($recipes as $recipeData) {
                // Use updateOrCreate to ensure existing recipes are updated or new ones are created, assigned to $user->id
                Recipe::updateOrCreate(
                    ['user_id' => $user->id, 'name' => $recipeData['name']],
                    array_merge($recipeData, ['user_id' => $user->id])
                );
            }
        }

        // 3. Call other seeders if needed
        // $this->call(NutrientSeeder::class);
    }
}