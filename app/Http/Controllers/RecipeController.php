/**
 * Class RecipeController
 * 用于处理食谱的 CRUD 操作 (Create, Read, Update, Delete)。
 * 这是一个 API Controller，所有返回值为 JSON 响应。
 * @package App\Http\Controllers
 */
class RecipeController extends Controller
{
    /**
     * 规则定义了食谱数据存储时的格式要求。
     * @var array<string, string>
     */
    private array $validationRules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'ingredients' => 'nullable|string',
        'instructions' => 'nullable|string',
        'estimated_time' => 'required|integer|min:1',
        'calories_kcal' => 'required|numeric|min:0',
        'protein_g' => 'required|numeric|min:0',
        'fat_g' => 'required|numeric|min:0',
        'carbohydrates_g' => 'required|numeric|min:0',
        'fiber_g' => 'required|numeric|min:0',
        'carbon_footprint_g' => 'required|numeric|min:0',
    ];

    /**
     * 获取当前用户的所有食谱。
     */
    public function index()
    {
        // 确保用户已登录，否则返回 401 未授权
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 获取当前用户创建的所有食谱
        $recipes = Auth::user()->recipes()->get();

        return response()->json([
            'message' => 'Recipes retrieved successfully.',
            'data' => $recipes
        ], 200);
    }

    /**
     * 存储一个新的食谱。
     * * @param Request $request
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // 确保用户已登录
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 验证请求数据
        $validatedData = $request->validate($this->validationRules);

        // 创建食谱，并自动关联当前用户ID
        $recipe = Auth::user()->recipes()->create($validatedData);

        return response()->json([
            'message' => 'Recipe created successfully.',
            'data' => $recipe
        ], 201); // 201 Created 状态码
    }

    /**
     * 显示单个食谱的详细信息。
     *
     * @param Recipe $recipe
     */
    public function show(Recipe $recipe)
    {
        // 允许所有登录用户查看任何食谱
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'message' => 'Recipe retrieved successfully.',
            'data' => $recipe
        ], 200);
    }

    /**
     * 更新指定的食谱。
     *
     * @param Request $request
     * @param Recipe $recipe
     * @throws ValidationException
     */
    public function update(Request $request, Recipe $recipe)
    {
        // 授权检查：确保当前用户是食谱的所有者 (使用 Gate 检查)
        if (Gate::denies('manage-recipe', $recipe)) {
            return response()->json(['message' => 'Unauthorized. You do not own this recipe.'], 403);
        }
        
        // 验证请求数据
        $validatedData = $request->validate($this->validationRules);

        $recipe->update($validatedData);

        return response()->json([
            'message' => 'Recipe updated successfully.',
            'data' => $recipe
        ], 200);
    }

    /**
     * 删除指定的食谱。
     *
     * @param Recipe $recipe
     */
    public function destroy(Recipe $recipe)
    {
        // 授权检查：确保当前用户是食谱的所有者 (使用 Gate 检查)
        if (Gate::denies('manage-recipe', $recipe)) {
            return response()->json(['message' => 'Unauthorized. You do not own this recipe.'], 403);
        }
        
        $recipe->delete();

        return response()->json([
            'message' => 'Recipe deleted successfully.'
        ], 204); // 204 No Content 状态码
    }
}