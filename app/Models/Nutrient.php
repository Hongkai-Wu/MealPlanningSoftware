<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    protected $table = 'nutrition_facts';

    protected $fillable = [
        'recipe_id','calories','protein','carbohydrates','fat','fiber','sugar','sodium',
    ];

    protected $casts = [
        'calories'=>'float','protein'=>'float','carbohydrates'=>'float','fat'=>'float',
        'fiber'=>'float','sugar'=>'float','sodium'=>'float',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
