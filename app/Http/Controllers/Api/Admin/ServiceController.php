<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ValidatesEditorialEntry;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ValidatesEditorialEntry;

    public function index(): JsonResponse
    {
        return response()->json(Service::query()->orderBy('sort_order')->latest('updated_at')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $service = Service::query()->create($request->validate($this->editorialRules('services')));

        return response()->json($service, 201);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $service->update($request->validate($this->editorialRules('services', $service)));

        return response()->json($service->fresh());
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(null, 204);
    }
}
