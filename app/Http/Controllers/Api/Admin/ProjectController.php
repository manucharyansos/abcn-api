<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ValidatesEditorialEntry;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ValidatesEditorialEntry;

    public function index(): JsonResponse
    {
        return response()->json(Project::query()->orderByDesc('created_at')->orderByDesc('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $project = Project::query()->create($request->validate($this->rules()));

        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $project->update($request->validate($this->rules($project)));

        return response()->json($project->fresh());
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(null, 204);
    }

    private function rules(?Project $project = null): array
    {
        return $this->editorialRules('projects', $project, [
            'completed_at' => ['nullable', 'date'],
        ]);
    }
}
