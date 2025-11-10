<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 注意：将表名从 'recipe_nutrients' 统一为 Laravel 默认的命名约定 'recipe_nutrient'（单数形式，按字母顺序排列）
        Schema::create('recipe_nutrient', function (Blueprint $table) {
            // 1. 关联 Recipe 表的主键
            $table->foreignId('recipe_id')
                  ->constrained() // 关联到 'recipes' 表
                  ->onDelete('cascade'); // 级联删除
            
            // 2. 关联 Nutrient 表的主键
            $table->foreignId('nutrient_id')
                  ->constrained() // 关联到 'nutrients' 表
                  ->onDelete('cascade'); // 级联删除
                  
            // 3. 关键修复：添加 'amount' 字段 (您的 Seeder 正在写入此字段)
            // 假设 'amount' 是一个需要精确的小数值
            $table->decimal('amount', 8, 2)->comment('此食谱中该营养素的含量'); 
            
            // 将 recipe_id 和 nutrient_id 设为联合主键，确保每对食谱-营养素的组合是唯一的
            $table->primary(['recipe_id', 'nutrient_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 确保删除时也是正确的表名
        Schema::dropIfExists('recipe_nutrient');
    }
};
