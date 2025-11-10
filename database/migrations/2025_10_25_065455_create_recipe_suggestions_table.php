<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('recipe_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->text('reason'); // why this recipe was suggested
            $table->json('goal_improvements'); // which goals this recipe helps improve
            $table->boolean('is_applied')->default(false); // if user applied this suggestion
            $table->timestamp('suggested_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_suggestions');
    }
};
