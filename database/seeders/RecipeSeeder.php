<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder now includes all required nutrition, time, and carbon footprint fields
     * to match the updated 'recipes' table migration.
     */
    public function run()
    {
        // --- IMPORTANT: Temporarily disable and then re-enable foreign key checks ---
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // --- SECTION 1: Clear Tables ---
        DB::table('recipes')->truncate();


        // --- SECTION 2: Prepare Recipe Data (Including new required fields) ---
        $recipes = [
            [
                'name' => 'Grilled Chicken Salad',
                'description' => 'A quick, low-carb, high-protein lunch option.',
                
                // === New Fields (Required by updated Migration) ===
                'estimated_time' => 15, // 准备时间（分钟）
                'calories_kcal' => 350.5,
                'protein_g' => 45.2,
                'fat_g' => 12.8,
                'carbohydrates_g' => 5.1,
                'fiber_g' => 3.5,
                'carbon_footprint_g' => 850.0, // 较高的碳足迹（鸡肉）
                // ===============================================

                'ingredients' => json_encode([
                    '200g chicken breast', 
                    '50g mixed greens', 
                    '10g olive oil', 
                    'Salt and pepper'
                ]),
                'instructions' => json_encode([
                    'Prepare the chicken.', 
                    'Grill until cooked through.', 
                    'Toss with greens and dressing.'
                ]),
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Lentil Soup (Vegetarian)',
                'description' => 'A hearty and fiber-rich vegetarian soup, excellent for dinner.',
                
                // === New Fields (Required by updated Migration) ===
                'estimated_time' => 30, 
                'calories_kcal' => 420.0,
                'protein_g' => 22.0,
                'fat_g' => 8.5,
                'carbohydrates_g' => 65.0,
                'fiber_g' => 18.0,
                'carbon_footprint_g' => 120.0, // 较低的碳足迹（素食）
                // ===============================================

                'ingredients' => json_encode([
                    '1 cup dry lentils', 
                    '4 cups vegetable broth', 
                    '1 chopped carrot', 
                    '1 chopped celery stalk',
                    'Spices (cumin, turmeric)'
                ]),
                'instructions' => json_encode([
                    'Rinse lentils.', 
                    'Combine all ingredients in a pot and bring to a boil.', 
                    'Simmer for 25-30 minutes until lentils are tender.'
                ]),
                'created_at' => now(), 
                'updated_at' => now()
            ]
        ];

        // --- SECTION 3: Insert Recipes ---
        DB::table('recipes')->insert($recipes);
        
        // --- Re-enable foreign key checks ---
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}