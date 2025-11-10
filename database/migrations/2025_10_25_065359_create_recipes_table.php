<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            // Foreign key constrained to the users table, ensuring every recipe belongs to a user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            
            // Fix: Add the serving size column needed by the DatabaseSeeder
            $table->string('serving_size')->comment('e.g., 1 Salad Portion, 3 Eggs, 100g Cooked Rice'); 

            // Macronutrient fields
            $table->integer('calories')->comment('Total calories per serving');
            $table->float('protein')->comment('Protein in grams per serving');
            $table->float('carbs')->comment('Carbohydrates in grams per serving');
            $table->float('fat')->comment('Fat in grams per serving');
            
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};