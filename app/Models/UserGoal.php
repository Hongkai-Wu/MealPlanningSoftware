<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 用户目标模型 (User Goal Model)
 * 对应数据库中的 user_goals 表
 */
class UserGoal extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性 (Mass assignable attributes)
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nutrient_id',
        'target_value',
        'target_type', // 例如: 'min' (最小), 'max' (最大)
    ];

    /**
     * 获取设置该目标的用户 (Get the user that owns the goal)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取目标对应的营养成分 (Get the nutrient associated with the goal)
     */
    public function nutrient()
    {
        return $this->belongsTo(Nutrient::class);
    }
}