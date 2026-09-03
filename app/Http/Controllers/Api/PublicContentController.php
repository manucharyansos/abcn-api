<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;

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

    public function products(): JsonResponse
    {
        $products = Product::query()
            ->where('status', 'published')
            ->with('category:id,slug,translations')
            ->orderBy('sort_order')
            ->paginate(24);

        return response()->json($products);
    }

    public function product(string $slug): JsonResponse
    {
        return response()->json(
            Product::query()
                ->where('slug', $slug)
                ->where('status', 'published')
                ->with('category:id,slug,translations')
                ->firstOrFail()
        );
    }
}
