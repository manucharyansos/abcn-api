<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFilterAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PublicContentController extends Controller
{
    public function page(string $slug): JsonResponse
    {
        return response()->json(
            Page::query()->where('slug', $slug)->where('status', 'published')->firstOrFail()
        );
    }

    public function categories(): JsonResponse
    {
        $categories = ProductCategory::query()
            ->whereNull('parent_id')
            ->where('status', 'published')
            ->with(['children' => fn ($query) => $query->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    public function products(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'integer', 'exists:product_categories,id'],
            'search' => ['nullable', 'string', 'max:120'],
            'locale' => ['nullable', 'in:hy,en'],
            'filters' => ['nullable', 'array', 'max:12'],
            'filters.*' => ['required', 'string', 'max:120'],
        ]);

        foreach (array_keys($validated['filters'] ?? []) as $key) {
            if (! preg_match('/^[a-z0-9_-]+$/', (string) $key)) {
                throw ValidationException::withMessages(['filters' => 'A filter key is invalid.']);
            }
        }

        $products = Product::query()->where('status', 'published');

        if (isset($validated['category'])) {
            $products->whereIn('product_category_id', $this->categoryBranchIds($validated['category']));
        }

        if (isset($validated['search'])) {
            $search = $validated['search'];
            $products->where(function ($query) use ($search) {
                $query->where('slug', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('translations', 'like', "%{$search}%");
            });
        }

        $facetProductIds = (clone $products)->select('products.id');
        $facets = ProductFilterAttribute::query()
            ->whereIn('product_id', $facetProductIds)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('key')
            ->map(function ($attributes, string $key) {
                $first = $attributes->first();

                return [
                    'key' => $key,
                    'label' => $first->label,
                    'sort_order' => $first->sort_order,
                    'options' => $attributes
                        ->groupBy('option')
                        ->map(function ($optionAttributes, string $option) {
                            $optionAttribute = $optionAttributes->first();

                            return [
                                'value' => $option,
                                'label' => $optionAttribute->value,
                                'count' => $optionAttributes->pluck('product_id')->unique()->count(),
                            ];
                        })
                        ->values(),
                ];
            })
            ->sortBy('sort_order')
            ->values();

        foreach ($validated['filters'] ?? [] as $key => $option) {
            $products->whereHas('filterAttributes', function ($query) use ($key, $option) {
                $query->where('key', $key)->where('option', $option);
            });
        }

        $paginated = $products
            ->with(['category:id,slug,translations', 'filterAttributes'])
            ->orderBy('sort_order')
            ->paginate(24);

        return response()->json([
            ...$paginated->toArray(),
            'facets' => $facets,
        ]);
    }

    public function product(string $slug): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['category:id,slug,translations', 'filterAttributes'])
            ->firstOrFail();

        $relatedProducts = $product->product_category_id
            ? Product::query()
                ->where('status', 'published')
                ->where('product_category_id', $product->product_category_id)
                ->whereKeyNot($product->id)
                ->with(['category:id,slug,translations', 'filterAttributes'])
                ->orderByDesc('featured')
                ->orderBy('sort_order')
                ->limit(3)
                ->get()
            : collect();

        return response()->json([
            ...$product->toArray(),
            'related_products' => $relatedProducts,
        ]);
    }

    private function categoryBranchIds(int $rootId): array
    {
        $ids = [$rootId];
        $frontier = [$rootId];

        while ($frontier !== []) {
            $frontier = ProductCategory::query()
                ->whereIn('parent_id', $frontier)
                ->pluck('id')
                ->all();
            $ids = [...$ids, ...$frontier];
        }

        return array_values(array_unique($ids));
    }
}
