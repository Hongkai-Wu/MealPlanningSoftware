<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('biometric_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('weight', 8, 2)->nullable(); // in kg
            $table->integer('systolic_bp')->nullable(); // systolic blood pressure
            $table->integer('diastolic_bp')->nullable(); // diastolic blood pressure
            $table->decimal('bmi', 5, 2)->nullable();
            $table->date('measurement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('biometric_data');
    }
};
