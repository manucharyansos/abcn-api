<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ValidatesEditorialEntry;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    use ValidatesEditorialEntry;

    public function index(): JsonResponse
    {
        return response()->json(TeamMember::query()->orderByDesc('created_at')->orderByDesc('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $member = TeamMember::query()->create($request->validate($this->editorialRules('team_members')));
        return response()->json($member, 201);
    }

    public function show(TeamMember $team): JsonResponse
    {
        return response()->json($team);
    }

    public function update(Request $request, TeamMember $team): JsonResponse
    {
        $team->update($request->validate($this->editorialRules('team_members', $team)));
        return response()->json($team->fresh());
    }

    public function destroy(TeamMember $team): JsonResponse
    {
        $team->delete();
        return response()->json(null, 204);
    }
}
