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
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $requests = ContactRequest::query()
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
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

        return response()->json($contactRequest->fresh());
    }
}
