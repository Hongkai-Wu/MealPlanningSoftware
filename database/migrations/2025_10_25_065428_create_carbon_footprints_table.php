<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carbon_footprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->decimal('co2_emissions', 8, 2); // in kg CO2 equivalent
            $table->string('measurement_unit')->default('kg'); // kg CO2 per serving
            $table->text('calculation_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carbon_footprints');
    }
};
