<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ProductCategory::query()->with('parent:id,slug')->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $category = ProductCategory::create($this->validated($request));

        return response()->json($category, 201);
    }

    public function show(ProductCategory $productCategory): JsonResponse
    {
        return response()->json($productCategory->load(['parent:id,slug', 'children']));
    }

    public function update(Request $request, ProductCategory $productCategory): JsonResponse
    {
        $productCategory->update($this->validated($request, $productCategory));

        return response()->json($productCategory->fresh());
    }

    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        $productCategory->delete();

        return response()->json(null, 204);
    }

    private function validated(Request $request, ?ProductCategory $category = null): array
    {
        $parentRules = ['nullable', 'exists:product_categories,id'];

        if ($category) {
            $parentRules[] = Rule::notIn([$category->id]);
        }

        return $request->validate([
            'parent_id' => $parentRules,
            'slug' => ['required', 'alpha_dash', 'max:140', Rule::unique('product_categories')->ignore($category)],
            'status' => ['required', 'in:draft,published,archived'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
            'translations.hy.name' => ['required', 'string', 'max:180'],
            'translations.en.name' => ['required', 'string', 'max:180'],
        ]);
    }
}
