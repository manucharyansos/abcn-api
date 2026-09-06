<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:new,in_progress,completed,archived'],
            'type' => ['nullable', 'in:general,product_quote'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $requests = ContactRequest::query()
            ->with('product:id,slug,sku,translations,images')
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['type'] ?? null, fn ($query, $type) => $query->where('request_type', $type))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('product_name', 'like', "%{$search}%")
                        ->orWhere('product_sku', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return response()->json($requests);
    }

    public function update(Request $request, ContactRequest $contactRequest): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,completed,archived'],
        ]);

        $contactRequest->update($validated);

        return response()->json($contactRequest->fresh()->load('product:id,slug,sku,translations,images'));
    }
}
