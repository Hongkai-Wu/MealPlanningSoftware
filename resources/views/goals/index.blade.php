@extends('layouts.app')

@section('title', '我的膳食目标')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">我的膳食目标</h1>
        <a href="{{ route('goals.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            <!-- Plus Icon -->
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            设定新目标
        </a>
    </div>

    {{-- 成功或错误消息提示 --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
            <p class="font-bold">成功</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if (count($goals) > 0)
        {{-- 目标列表表格 --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="min-w-full">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                营养素
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                目标值
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                目标类型
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                设定日期
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($goals as $goal)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $goal->nutrient->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        单位: {{ $goal->nutrient->unit }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-semibold text-indigo-600">{{ number_format($goal->target_value, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($goal->is_up_goal)
                                        <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            多于/等于 (≥)
                                        </span>
                                    @else
                                        <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            少于/等于 (≤)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $goal->start_date ? \Carbon\Carbon::parse($goal->start_date)->isoFormat('YYYY年MM月DD日') : '未设定' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('goals.edit', $goal) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out mr-4">编辑</a>
                                    
                                    {{-- 删除表单，必须使用 POST/DELETE 方法 --}}
                                    <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline" onsubmit="return confirm('您确定要删除此目标吗？这将无法撤销。')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out">删除</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- 没有目标时的提示 --}}
        <div class="text-center py-12 border border-dashed border-gray-300 rounded-lg bg-gray-50">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l-2-2H4a1 1 0 00-1 1v14a1 1 0 001 1h14a1 1 0 001-1v-2M9 19h10a1 1 0 001-1v-5m-10 5V8m0 11a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">您尚未设定任何膳食目标</h3>
            <p class="mt-1 text-sm text-gray-500">
                立即设定您的第一个营养摄入目标，开始您的健康之旅。
            </p>
            <div class="mt-6">
                <a href="{{ route('goals.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <!-- Plus Icon -->
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    设定新目标
                </a>
            </div>
        </div>
    @endif
</div>
@endsection