<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biometric extends Model
{
    use HasFactory;

    // 对应迁移文件中的表名
    protected $table = 'biometric_data';

    protected $fillable = [
        'user_id',
        'weight',
        'systolic_bp',
        'diastolic_bp',
        'bmi',
        'measurement_date',
        'notes',
    ];

    protected $casts = [
        'measurement_date' => 'date',
        'weight' => 'float',
        'bmi' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}