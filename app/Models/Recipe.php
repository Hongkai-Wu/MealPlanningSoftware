<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'serving_size',
        'calories',
        'protein',
        'carbs',
        'fat',
        'description',
    ];

    protected $casts = [
        'calories' => 'float',
        'protein'  => 'float',
        'carbs'    => 'float',
        'fat'      => 'float',
    ];

    // 一道食谱属于一个用户（非常重要）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 一道食谱可以出现在多个 meal_logs（Dashboard 会用到）
    public function mealLogs()
    {
        return $this->hasMany(MealLog::class);
    }

    // nutrition_facts 表（一对一）
    public function nutrient()
    {
        return $this->hasOne(Nutrient::class, 'recipe_id');
    }

    // 可选别名
    public function nutritionFact()
    {
        return $this->hasOne(Nutrient::class, 'recipe_id');
    }
   
   public function carbonFootprint()
    {
        return $this->hasOne(CarbonFootprint::class);
   }
  
}