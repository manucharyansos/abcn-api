<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:draft,published,archived'],
            'category' => ['nullable', 'integer', 'exists:product_categories,id'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        return response()->json(
            Product::query()
                ->with('category:id,slug,translations')
                ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->when($validated['category'] ?? null, fn ($query, $category) => $query->where('product_category_id', $category))
                ->when($validated['search'] ?? null, function ($query, $search) {
                    $query->where(function ($nested) use ($search) {
                        $nested->where('slug', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('translations', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(40)
                ->withQueryString()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $product = Product::create($this->validated($request));

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category:id,slug,translations'));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $product->update($this->validated($request, $product));

        return response()->json($product->fresh());
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'slug' => ['required', 'alpha_dash', 'max:160', Rule::unique('products')->ignore($product)],
            'sku' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:draft,published,archived'],
            'featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
            'translations.hy.name' => ['required', 'string', 'max:220'],
            'translations.hy.description' => ['nullable', 'string', 'max:5000'],
            'translations.en.name' => ['required', 'string', 'max:220'],
            'translations.en.description' => ['nullable', 'string', 'max:5000'],
            'specifications' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'documents' => ['nullable', 'array'],
        ]);
    }
}
