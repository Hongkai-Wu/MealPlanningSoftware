<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarbonFootprint extends Model
{
    use HasFactory;

    // 对应迁移中的表 carbon_footprints
    protected $fillable = [
        'recipe_id',
        'co2_emissions',     // 每份的 CO2e
        'measurement_unit',  // 默认 kg
        'calculation_notes',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}