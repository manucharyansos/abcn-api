<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ValidatesEditorialEntry;
use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsArticleController extends Controller
{
    use ValidatesEditorialEntry;

    public function index(): JsonResponse
    {
        return response()->json(
            NewsArticle::query()->orderByDesc('created_at')->orderByDesc('id')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $article = NewsArticle::query()->create($request->validate($this->rules()));

        return response()->json($article, 201);
    }

    public function show(NewsArticle $newsArticle): JsonResponse
    {
        return response()->json($newsArticle);
    }

    public function update(Request $request, NewsArticle $newsArticle): JsonResponse
    {
        $newsArticle->update($request->validate($this->rules($newsArticle)));

        return response()->json($newsArticle->fresh());
    }

    public function destroy(NewsArticle $newsArticle): JsonResponse
    {
        $newsArticle->delete();

        return response()->json(null, 204);
    }

    private function rules(?NewsArticle $article = null): array
    {
        return $this->editorialRules('news_articles', $article, [
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
