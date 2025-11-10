<?php

namespace App\Providers;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // 定义一个通用的“管理食谱”权限，涵盖更新和删除
        Gate::define('manage-recipe', function (User $user, Recipe $recipe) {
            // 只有食谱的创建者 (user_id) 可以管理 (更新/删除) 它
            return $user->id === $recipe->user_id;
        });
    }
}
