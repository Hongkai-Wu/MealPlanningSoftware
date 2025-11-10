<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SystemRecipesSeeder extends Seeder
{
    public function run(): void
    {
        // 1) system user
        $systemId = DB::table('users')->where('email','system@demo.local')->value('id');
        if (!$systemId) {
            $systemId = DB::table('users')->insertGetId([
                'name' => 'System Library',
                'email' => 'system@demo.local',
                'password' => bcrypt(Str::random(16)),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // 2) recipes to seed
        $recipes = [
            [
                'name'=>'Brown Rice (100g)',
                'serving_size'=>'100 g',
                'calories'=>120,'protein'=>2.6,'carbs'=>25,'fat'=>1.0,
                'description'=>'Cooked brown rice'
            ],
            [
                'name'=>'Chicken Breast (100g)',
                'serving_size'=>'100 g',
                'calories'=>165,'protein'=>31,'carbs'=>0,'fat'=>3.6,
                'description'=>'Skinless chicken breast'
            ],
            [
                'name'=>'Greek Yogurt (150g)',
                'serving_size'=>'150 g',
                'calories'=>150,'protein'=>15,'carbs'=>8,'fat'=>4,
                'description'=>'Plain Greek yogurt'
            ],
        ];

        foreach ($recipes as $r) {
            // skip if the recipe already exists for system user
            $rid = DB::table('recipes')
                ->where('user_id', $systemId)
                ->where('name', $r['name'])
                ->value('id');

            if (!$rid) {
                $rid = DB::table('recipes')->insertGetId(array_merge($r, [
                    'user_id'=>$systemId, 'created_at'=>now(), 'updated_at'=>now(),
                ]));
            }

            // nutrition_facts: insert if not exists
            $nfExists = DB::table('nutrition_facts')->where('recipe_id', $rid)->exists();
            if (!$nfExists) {
                DB::table('nutrition_facts')->insert([
                    'recipe_id'=>$rid,
                    'calories'=>$r['calories'],
                    'protein'=>$r['protein'],
                    'carbohydrates'=>$r['carbs'],
                    'fat'=>$r['fat'],
                    'fiber'=>$r['name']==='Brown Rice (100g)' ? 1.8 : 0.0,
                    'sugar'=>$r['name']==='Greek Yogurt (150g)' ? 6.0 : 0.0,
                    'sodium'=> $r['name']==='Chicken Breast (100g)' ? 60 : 0,
                    'created_at'=>now(), 'updated_at'=>now(),
                ]);
            }

            // carbon_footprints: only if table exists, and not inserted yet
            if (Schema::hasTable('carbon_footprints')) {
                $cfExists = DB::table('carbon_footprints')->where('recipe_id', $rid)->exists();
                if (!$cfExists) {
                    DB::table('carbon_footprints')->insert([
                        'recipe_id'=>$rid,
                        'co2_emissions'=> match ($r['name']) {
                            'Chicken Breast (100g)' => 1.2,
                            'Greek Yogurt (150g)'  => 0.7,
                            default                => 0.3,
                        },
                        'measurement_unit'=>'kg',
                        'calculation_notes'=>'demo per serving',
                        'created_at'=>now(), 'updated_at'=>now(),
                    ]);
                }
            }
        }
    }
}