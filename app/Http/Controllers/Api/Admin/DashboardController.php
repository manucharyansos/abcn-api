<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\Product;
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
                'media' => Media::query()->count(),
            ],
            'requests' => ContactRequest::query()->latest()->limit(20)->get(),
        ]);
    }
}
