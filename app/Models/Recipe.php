<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id','name','serving_size','calories','protein','carbs','fat','description',
    ];

    protected $casts = [
        'calories'=>'float','protein'=>'float','carbs'=>'float','fat'=>'float',
    ];

    // points to table: nutrition_facts via Nutrient model
    public function nutrient()
    {
        return $this->hasOne(Nutrient::class, 'recipe_id');
    }

    // optional alias; some views/controllers may call nutritionFact()
    public function nutritionFact()
    {
        return $this->hasOne(Nutrient::class, 'recipe_id');
    }
}