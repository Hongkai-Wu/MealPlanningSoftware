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
        // 创建 recipes 表
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            
            // 1. 食谱名称 (我们上次修复的)
            $table->string('name')->unique()->comment('食谱名称'); 
            
            // 2. 食谱描述/步骤 (上次修复的，这里拆分得更细致)
            $table->text('description')->nullable()->comment('食谱简介');
            
            // 3. 关键修复：添加 ingredients 字段 (您的 Seeder 正在写入此字段)
            // 这里的 ingredients 应该存储配料列表，通常用 JSON 字符串或 TEXT 存储。
            $table->text('ingredients')->comment('食谱配料列表 (例如JSON格式)');
            
            // 4. 关键修复：添加 instructions 字段 (您的 Seeder 正在写入此字段)
            $table->text('instructions')->comment('详细烹饪步骤');
            
            // 5. 关键修复：添加 estimated_time 字段 (您的 Seeder 正在写入此字段)
            $table->integer('estimated_time')->nullable()->comment('预计所需时间 (分钟)');
            
            // 6. 关键修复：添加 servings 字段 (您的 Seeder 正在写入此字段)
            $table->integer('servings')->nullable()->comment('可供食用份数');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
