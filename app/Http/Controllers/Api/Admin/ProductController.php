<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                ->with(['category:id,slug,translations', 'filterAttributes'])
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
        $validated = $this->validated($request);
        $filterAttributes = $validated['filter_attributes'] ?? [];
        unset($validated['filter_attributes']);

        $product = DB::transaction(function () use ($validated, $filterAttributes) {
            $product = Product::create($validated);
            $product->filterAttributes()->createMany($filterAttributes);

            return $product;
        });

        return response()->json($product->load(['category:id,slug,translations', 'filterAttributes']), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load(['category:id,slug,translations', 'filterAttributes']));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $this->validated($request, $product);
        $filterAttributes = $validated['filter_attributes'] ?? [];
        unset($validated['filter_attributes']);

        DB::transaction(function () use ($product, $validated, $filterAttributes) {
            $product->update($validated);
            $product->filterAttributes()->delete();
            $product->filterAttributes()->createMany($filterAttributes);
        });

        return response()->json($product->fresh()->load(['category:id,slug,translations', 'filterAttributes']));
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
            'filter_attributes' => ['nullable', 'array', 'max:16'],
            'filter_attributes.*.key' => ['required', 'alpha_dash', 'max:80', 'distinct:strict'],
            'filter_attributes.*.option' => ['required', 'alpha_dash', 'max:120'],
            'filter_attributes.*.label' => ['required', 'array'],
            'filter_attributes.*.label.hy' => ['required', 'string', 'max:120'],
            'filter_attributes.*.label.en' => ['required', 'string', 'max:120'],
            'filter_attributes.*.value' => ['required', 'array'],
            'filter_attributes.*.value.hy' => ['required', 'string', 'max:160'],
            'filter_attributes.*.value.en' => ['required', 'string', 'max:160'],
            'filter_attributes.*.sort_order' => ['sometimes', 'integer', 'min:0'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*.url' => ['required', 'string', 'max:2048'],
            'images.*.name' => ['nullable', 'string', 'max:255'],
            'images.*.alt' => ['nullable', 'array'],
            'images.*.alt.hy' => ['nullable', 'string', 'max:500'],
            'images.*.alt.en' => ['nullable', 'string', 'max:500'],
            'documents' => ['nullable', 'array'],
        ]);
    }
}
