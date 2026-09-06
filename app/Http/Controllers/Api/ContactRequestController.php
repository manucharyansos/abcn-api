<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:hy,en'],
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:5000'],
            'product_slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('products', 'slug')->where(fn ($query) => $query->where('status', 'published')),
            ],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:1000000'],
        ]);

        $product = isset($validated['product_slug'])
            ? Product::query()->where('slug', $validated['product_slug'])->where('status', 'published')->first()
            : null;
        $productName = $product
            ? ($product->translations[$validated['locale']]['name'] ?? $product->translations['en']['name'] ?? $product->slug)
            : null;

        $contactRequest = ContactRequest::create([
            'locale' => $validated['locale'],
            'request_type' => $product ? 'product_quote' : 'general',
            'product_id' => $product?->id,
            'product_name' => $productName,
            'product_sku' => $product?->sku,
            'quantity' => $product ? ($validated['quantity'] ?? null) : null,
            'name' => $validated['name'],
            'company' => $validated['company'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
        ]);

        return response()->json([
            'message' => 'Your inquiry has been received.',
            'id' => $contactRequest->id,
        ], 201);
    }
}
