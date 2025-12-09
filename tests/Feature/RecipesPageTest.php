<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipesPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_is_redirected_to_login_when_visiting_recipes_index()
    {
        $response = $this->get('/recipes');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_sees_his_own_recipes_on_index_page()
    {
        
        $user = User::factory()->create();


        $recipesForUser = Recipe::factory()
            ->count(3)
            ->for($user) 
            ->create();

      
        Recipe::factory()->count(2)->create();

       
        $response = $this->actingAs($user)->get('/recipes');

    
        $response->assertStatus(200);

        
        foreach ($recipesForUser as $recipe) {
            $response->assertSeeText($recipe->name);
        }

        
    }
}
