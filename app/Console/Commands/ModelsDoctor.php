<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ModelsDoctor extends Command
{
    protected $signature = 'models:doctor';
    protected $description = 'Check model ↔ database consistency for key models';

    public function handle(): int
    {
        $checks = [
            // table => required columns
            'recipes' => ['id','user_id','name','serving_size','calories','protein','carbs','fat','created_at','updated_at'],
            'nutrition_facts' => ['id','recipe_id','calories','protein','carbohydrates','fat','fiber','sugar','sodium','created_at','updated_at'],
            'calendar_entries' => ['id','user_id','recipe_id','date','meal_type','servings','created_at','updated_at'],
            'user_goals' => ['id','user_id','goal_type','target_value','direction','unit','start_date','end_date','is_active','created_at','updated_at'],
        ];

        $ok = true;

        foreach ($checks as $table => $cols) {
            if (!Schema::hasTable($table)) {
                $this->error("❌ Missing table: {$table}");
                $ok = false;
                continue;
            }
            $missing = array_filter($cols, fn($c) => !Schema::hasColumn($table, $c));
            if ($missing) {
                $this->warn("⚠️  {$table} missing columns: ".implode(', ', $missing));
                $ok = false;
            } else {
                $this->info("✅ {$table} — columns OK");
            }
        }

        // soft sanity notes
        $this->line('');
        $this->comment('Model relation checklist (manual verify quickly):');
        $this->line(' - App\\Models\\Recipe: hasOne Nutrient (recipe_id), belongsTo User');
        $this->line(' - App\\Models\\Nutrient: belongsTo Recipe');
        $this->line(' - App\\Models\\CalendarEntry: belongsTo User, belongsTo Recipe, accessors: nutrition, co2e(optional)');
        $this->line(' - App\\Models\\UserGoal: scopeActive, casts for dates/boolean');

        $this->line('');
        $this->comment('If you see ⚠️, paste the warning给我，我发你精确修复代码块。');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
