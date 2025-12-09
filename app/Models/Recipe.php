<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'serving_size',
        'calories',
        'protein',
        'carbs',
        'fat',
       'fiber',
        'description',
    ];

    protected $casts = [
        'calories' => 'float',
        'protein'  => 'float',
        'carbs'    => 'float',
        'fat'      => 'float',
        'fiber'    => 'float', 
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function mealLogs()
    {
        return $this->hasMany(MealLog::class);
    }

   
    public function nutrient()
    {
        return $this->hasOne(Nutrient::class, 'recipe_id');
    }

   
    public function nutritionFact()
    {
        return $this->hasOne(Nutrient::class, 'recipe_id');
    }
   
   public function carbonFootprint()
    {
        return $this->hasOne(CarbonFootprint::class);
   }
  
}