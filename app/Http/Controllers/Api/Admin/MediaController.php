<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Media::query()->latest()->paginate(60));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
            'alt.hy' => ['nullable', 'string', 'max:220'],
            'alt.en' => ['nullable', 'string', 'max:220'],
        ]);

        $file = $validated['file'];
        $path = $file->store('media/'.now()->format('Y/m'), 'public');

        $media = Media::create([
            'uploaded_by' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
            'kind' => $file->getMimeType() === 'application/pdf' ? 'document' : 'image',
            'size' => $file->getSize(),
            'alt' => $validated['alt'] ?? null,
        ]);

        return response()->json($media, 201);
    }

    public function destroy(Media $media): JsonResponse
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(null, 204);
    }
}
