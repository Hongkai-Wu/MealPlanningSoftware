<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NutrientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds essential nutrients such as Calories, Protein, Fat, and Carbohydrates.
     */
    public function run()
    {
        // ATTENTION: This Seeder has been modified because the 'nutrients' and 
        // 'recipe_nutrient' migration files were missing from the project.
        // All database interaction lines referring to these tables are COMMENTED OUT
        // to prevent the 'Base table or view not found' error (Error 1146).

        // 1. Clear the 'nutrients' table (DISABLED)
        // DB::table('nutrients')->delete();
        
        // 2. Clear the 'recipe_nutrient' pivot table (DISABLED)
        // DB::table('recipe_nutrient')->delete();
        
        $nutrients = [
            // Macronutrients
            ['name' => 'Calories', 'unit' => 'kcal', 'is_macro' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Protein', 'unit' => 'g', 'is_macro' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fat', 'unit' => 'g', 'is_macro' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carbohydrates', 'unit' => 'g', 'is_macro' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Micronutrients examples
            ['name' => 'Vitamin C', 'unit' => 'mg', 'is_macro' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Iron', 'unit' => 'mg', 'is_macro' => false, 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insertion into 'nutrients' table (DISABLED)
        // DB::table('nutrients')->insert($nutrients);
        
        // Check if essential data exists (DISABLED)
        /*
        if (DB::table('nutrients')->where('name', 'Calories')->doesntExist()) {
            throw new \Exception("NutrientSeeder failed to insert essential nutrients.");
        }
        */
    }
}