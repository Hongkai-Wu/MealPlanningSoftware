<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\CarbonFootprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::where('user_id', Auth::id())
            ->with('carbonFootprint')
            ->orderBy('name')
            ->get();

        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'serving_size' => ['required', 'string', 'max:255'],
            'calories'     => ['required', 'numeric', 'min:0'],
            'protein'      => ['required', 'numeric', 'min:0'],
            'carbs'        => ['required', 'numeric', 'min:0'],
            'fat'          => ['required', 'numeric', 'min:0'], 
            'fiber'        => 'nullable|numeric|min:0', 

            'description'  => ['nullable', 'string'],

            // 新增：碳足迹字段（可选）
            'co2_emissions'    => ['nullable', 'numeric', 'min:0'],
            'co2_notes'        => ['nullable', 'string'],
        ]);

        $recipe = Recipe::create([
            'user_id'      => Auth::id(),
            'name'         => $data['name'],
            'serving_size' => $data['serving_size'],
            'calories'     => $data['calories'],
            'protein'      => $data['protein'],
            'carbs'        => $data['carbs'],
            'fat'          => $data['fat'],
           'fiber'        => $data['fiber'] ?? 0,
          
            'description'  => $data['description'] ?? null,
        ]);

        
        if (!empty($data['co2_emissions'])) {
            CarbonFootprint::create([
                'recipe_id'        => $recipe->id,
                'co2_emissions'    => $data['co2_emissions'],
                'measurement_unit' => 'kg',  // 约定为 kg CO2e per serving
                'calculation_notes'=> $data['co2_notes'] ?? null,
            ]);
        }

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe created successfully.');
    }

    public function edit(Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);

        $recipe->load('carbonFootprint');

        return view('recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'serving_size' => ['required', 'string', 'max:255'],
            'calories'     => ['required', 'numeric', 'min:0'],
            'protein'      => ['required', 'numeric', 'min:0'],
            'carbs'        => ['required', 'numeric', 'min:0'],
            'fat'          => ['required', 'numeric', 'min:0'],
           'fiber'        => 'nullable|numeric|min:0', 
            'description'  => ['nullable', 'string'],

            'co2_emissions'    => ['nullable', 'numeric', 'min:0'],
            'co2_notes'        => ['nullable', 'string'],
        ]);

        $recipe->update([
            'name'         => $data['name'],
            'serving_size' => $data['serving_size'],
            'calories'     => $data['calories'],
            'protein'      => $data['protein'],
            'carbs'        => $data['carbs'],
            'fat'          => $data['fat'],
           'fiber'        => $data['fiber'] ?? 0,
            'description'  => $data['description'] ?? null,
        ]);

        // 处理碳足迹
        $co2 = $data['co2_emissions'] ?? null;
        $notes = $data['co2_notes'] ?? null;

        if ($co2 !== null && $co2 !== '') {
            if ($recipe->carbonFootprint) {
                $recipe->carbonFootprint->update([
                    'co2_emissions'    => $co2,
                    'calculation_notes'=> $notes,
                ]);
            } else {
                CarbonFootprint::create([
                    'recipe_id'        => $recipe->id,
                    'co2_emissions'    => $co2,
                    'measurement_unit' => 'kg',
                    'calculation_notes'=> $notes,
                ]);
            }
        } else {
           
            if ($recipe->carbonFootprint) {
                $recipe->carbonFootprint->delete();
            }
        }

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);
        $recipe->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe deleted successfully.');
    }

    protected function authorizeRecipe(Recipe $recipe)
    {
        if ($recipe->user_id !== Auth::id()) {
            abort(403);
        }
    }
}