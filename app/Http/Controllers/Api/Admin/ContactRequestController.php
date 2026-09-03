<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ContactRequest::query()->latest()->paginate(30));
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
