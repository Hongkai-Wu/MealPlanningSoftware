<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nutrition_facts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0); // in grams
            $table->decimal('carbohydrates', 8, 2)->default(0); // in grams
            $table->decimal('fat', 8, 2)->default(0); // in grams
            $table->decimal('fiber', 8, 2)->default(0); // in grams
            $table->decimal('sugar', 8, 2)->default(0); // in grams
            $table->decimal('sodium', 8, 2)->default(0); // in mg
            $table->decimal('vitamin_a', 8, 2)->default(0); // in mcg
            $table->decimal('vitamin_c', 8, 2)->default(0); // in mg
            $table->decimal('calcium', 8, 2)->default(0); // in mg
            $table->decimal('iron', 8, 2)->default(0); // in mg
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nutrition_facts');
    }
};
