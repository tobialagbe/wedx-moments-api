<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Media;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MediaController extends Controller
{
    public function store(Request $request, Event $event)
    {
        try {
            $this->authorize('update', $event);

            $request->validate([
                'media' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:2048',
            ]);

            $mediaUrl = $this->uploadMediaToCloudinary($request->file('media'));

            $media = $event->media()->create([
                'url' => $mediaUrl,
            ]);

            return $this->successResponse($media);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function destroy(Media $media)
    {
        try {
            $this->authorize('delete', $media);
            $media->delete();
            return $this->successResponse('Media deleted successfully');
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    protected function uploadMediaToCloudinary($file)
    {
        try {
            $uploadedFileUrl = Cloudinary::uploadFile($file->getRealPath())->getSecurePath();
            return $uploadedFileUrl;
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
