<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 运行迁移
     */
    public function up(): void
    {
        // 这个表用于记录用户消费的“食谱条目” (Recipe Consumption Entry)
        // 对应 MealLogController 的写入操作。
        Schema::create('meal_logs', function (Blueprint $table) {
            $table->id();

            // 关联到 users 表
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 关联到 recipes 表 (必需字段)
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            
            // 消费份量 (必需字段)
            $table->decimal('servings_consumed', 8, 2); 
            
            // 消费时间
            $table->timestamp('consumed_at')->useCurrent();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 回滚迁移
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_logs');
    }
};