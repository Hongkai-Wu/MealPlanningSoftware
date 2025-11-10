<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 运行迁移。
     */
    public function up(): void
    {
        Schema::create('nutrients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('营养素或环境指标名称');
            $table->string('unit')->comment('营养素的计量单位');
            
            // <-- 重点：添加缺失的 is_macro 字段 -->
            $table->boolean('is_macro')->default(false)->comment('是否为宏量营养素 (true) 或微量营养素 (false)');
            // <-- 重点结束 -->
            
            $table->timestamps();
        });
    }

    /**
     * 回滚迁移。
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrients');
    }
};
