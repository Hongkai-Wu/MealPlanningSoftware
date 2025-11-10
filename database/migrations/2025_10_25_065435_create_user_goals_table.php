<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('user_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('goal_type'); // protein, fiber, calories, carbohydrates, etc.
            $table->decimal('target_value', 8, 2);
            $table->enum('direction', ['up', 'down']); // up for increasing, down for decreasing
            $table->string('unit'); // grams, mg, kg, etc.
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_goals');
    }
};
