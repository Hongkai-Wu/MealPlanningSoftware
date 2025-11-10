<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEntry extends Model
{
    protected $fillable = [
        'user_id',
        'recipe_id',
        'date',
        'meal_type',
        'servings',
    ];

    protected $casts = [
        'date' => 'date',
        'servings' => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function recipe() { return $this->belongsTo(Recipe::class); }

    public function getNutritionAttribute(): array
    {
        $s = max(1, (int) $this->servings);
        $r = $this->recipe;
        $nu = $r?->nutrient;

        $base = [
            'calories' => (float) ($nu->calories ?? $r->calories ?? 0),
            'protein'  => (float) ($nu->protein ?? $r->protein ?? 0),
            'carbs'    => (float) ($nu->carbohydrates ?? $r->carbs ?? 0),
            'fat'      => (float) ($nu->fat ?? $r->fat ?? 0),
            'fiber'    => (float) ($nu->fiber ?? 0),
            'sugar'    => (float) ($nu->sugar ?? 0),
            'sodium'   => (float) ($nu->sodium ?? 0),
        ];

        foreach ($base as $k => $v) { $base[$k] = round($v * $s, 2); }
        return $base;
    }
}
