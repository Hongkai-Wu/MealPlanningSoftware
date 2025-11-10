<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    use HasFactory;

    // 允许通过 create() 方法批量赋值的字段
    protected $fillable = [
        'user_id',
        'recipe_id',
        'servings_consumed',
        'consumed_at',
    ];

    // 将 consumed_at 字段自动转换为 Carbon 实例
    protected $casts = [
        'consumed_at' => 'datetime',
    ];

    /**
     * Meal Log 关联到 Recipe 模型 (多对一)
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Meal Log 关联到 User 模型 (多对一)
     */
    public function user()
    {
        // 假设您的用户模型是 App\Models\User
        return $this->belongsTo(User::class);
    }
}