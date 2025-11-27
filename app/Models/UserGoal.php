<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGoal extends Model
{
    use HasFactory;

    protected $table = 'user_goals';

    /**
     * 与数据库字段一致的 fillable
     */
    protected $fillable = [
        'user_id',
        'goal_type',     // protein, calories, carbs, fat, fiber, co2_emissions
        'target_value',
        'direction',     // up / down
        'unit',          // g, kcal, mg, kg CO2e
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * 字段类型转换
     */
    protected $casts = [
        'target_value' => 'float',
        'is_active'    => 'boolean',
        'start_date'   => 'date',
        'end_date'     => 'date',
    ];

    /**
     * 一个目标属于一个用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}