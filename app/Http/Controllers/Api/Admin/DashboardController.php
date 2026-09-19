<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\Media;
use App\Models\NewsArticle;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'counts' => [
                'new_requests' => ContactRequest::query()->where('status', 'new')->count(),
                'total_requests' => ContactRequest::query()->count(),
                'pages' => Page::query()->where('status', 'published')->count(),
                'products' => Product::query()->where('status', 'published')->count(),
                'services' => Service::query()->where('status', 'published')->count(),
                'projects' => Project::query()->where('status', 'published')->count(),
                'news' => NewsArticle::query()->where('status', 'published')->count(),
                'media' => Media::query()->count(),
            ],
            'requests' => ContactRequest::query()
                ->with('product:id,slug,sku,translations,images')
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }
}
