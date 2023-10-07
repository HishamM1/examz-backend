<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;

class UploadImageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $model, int $modelId, ImageService $image_service)
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,mp4,mov,avi,webm']
        ]);
                
        $image_name = $image_service->update($request->file('image'), $model, $modelId);

        return response()->json([
            'image' => $image_name,
            'message' => 'Image uploaded successfully.',
        ], 200);
    }
}
