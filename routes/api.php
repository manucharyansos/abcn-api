<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\ContactRequestController as AdminContactRequestController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\Admin\NewsArticleController;
use App\Http\Controllers\Api\Admin\PageController;
use App\Http\Controllers\Api\Admin\ProductCategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\ContactRequestController;
use App\Http\Controllers\Api\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => ['status' => 'ok', 'service' => 'ABCN API']);

    Route::get('/homepage', [PublicContentController::class, 'homepage']);
    Route::get('/pages/{slug}', [PublicContentController::class, 'page']);
    Route::get('/services', [PublicContentController::class, 'services']);
    Route::get('/services/{slug}', [PublicContentController::class, 'service']);
    Route::get('/projects', [PublicContentController::class, 'projects']);
    Route::get('/projects/{slug}', [PublicContentController::class, 'project']);
    Route::get('/news', [PublicContentController::class, 'news']);
    Route::get('/news/{slug}', [PublicContentController::class, 'newsArticle']);
    Route::get('/product-categories', [PublicContentController::class, 'categories']);
    Route::get('/products', [PublicContentController::class, 'products']);
    Route::get('/products/compare', [PublicContentController::class, 'compare']);
    Route::get('/products/{slug}', [PublicContentController::class, 'product']);
    Route::post('/contact-requests', [ContactRequestController::class, 'store'])->middleware('throttle:contact');

    Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:admin-login');

    Route::prefix('admin')->middleware('auth.admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', DashboardController::class);
        Route::get('/contact-requests', [AdminContactRequestController::class, 'index']);
        Route::patch('/contact-requests/{contactRequest}', [AdminContactRequestController::class, 'update']);
        Route::get('/media', [MediaController::class, 'index']);
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{media}', [MediaController::class, 'destroy']);
        Route::apiResource('pages', PageController::class);
        Route::apiResource('product-categories', ProductCategoryController::class)
            ->parameters(['product-categories' => 'productCategory']);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('news', NewsArticleController::class)
            ->parameters(['news' => 'newsArticle']);
    });
});
