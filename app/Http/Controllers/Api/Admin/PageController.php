<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Page::query()->orderBy('slug')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $page = Page::create($this->validated($request));

        return response()->json($page, 201);
    }

    public function show(Page $page): JsonResponse
    {
        return response()->json($page);
    }

    public function update(Request $request, Page $page): JsonResponse
    {
        $page->update($this->validated($request, $page));

        return response()->json($page->fresh());
    }

    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json(null, 204);
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        return $request->validate([
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('pages')->ignore($page)],
            'status' => ['required', 'in:draft,published,archived'],
            'content' => ['required', 'array'],
            'content.hy' => ['required', 'array'],
            'content.en' => ['required', 'array'],
            'meta' => ['nullable', 'array'],
        ]);
    }
}
