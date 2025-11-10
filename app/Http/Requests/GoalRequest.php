<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // 确保只有认证过的用户才能创建或更新目标
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // 此规则集适用于创建 (store) 和更新 (update) 目标
        return [
            // 确保 nutrient_id 必须存在于 'nutrients' 表中
            'nutrient_id' => [
                'required', 
                'integer', 
                'exists:nutrients,id',
            ],
            // 目标值必须是数字，且大于 0
            'target_value' => [
                'required', 
                'numeric', 
                'min:0.01'
            ],
            // 目标类型 (高于或低于目标) 必须是布尔值 (0 或 1)
            'is_up_goal' => [
                'required', 
                'boolean'
            ],
            // 开始日期是可选的，但如果填写，必须是有效的日期格式
            'start_date' => [
                'nullable', 
                'date'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nutrient_id.required' => '请选择一个营养素来设定目标。',
            'nutrient_id.exists' => '所选营养素无效，请重新选择。',
            'target_value.required' => '目标值是必须填写的。',
            'target_value.numeric' => '目标值必须是数字类型。',
            'target_value.min' => '目标值必须大于0。',
            'is_up_goal.required' => '必须选择目标类型（高于目标或低于目标）。',
            'is_up_goal.boolean' => '目标类型选择无效。',
        ];
    }
}
