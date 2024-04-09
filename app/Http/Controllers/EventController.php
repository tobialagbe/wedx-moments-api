<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Auth::user()->events;
            return $this->successResponse($events);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'visibility' => 'required|boolean',
            ]);

            Model::unguard();
            $event = new Event([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'visibility' => $request->visibility,
            ]);

            $event->save();

            return $this->successResponse($event);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function uploadCoverImage(Request $request, $eventId)
    {
        try {
            $request->validate([
                'cover_image' => 'required|image',
            ]);

            $event = Event::findOrFail($eventId);

            if ($event->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $coverImageUrl = $this->uploadImageToCloudinary($request->file('cover_image'));

            $event->cover_image = $coverImageUrl;
            $event->save();

            $response = ['message' => 'Cover image uploaded successfully', 'cover_image_url' => $coverImageUrl];
            return $this->successResponse($response);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function show(Event $event)
    {
        try {
            $this->authorize('view', $event);
            return response()->json($event);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function update(Request $request, Event $event)
    {
        try {
            $this->authorize('update', $event);

            $request->validate([
                'name' => 'string',
                'cover_image' => 'image',
                'visibility' => 'boolean',
            ]);

            if ($request->hasFile('cover_image')) {
                $coverImageUrl = $this->uploadImageToCloudinary($request->file('cover_image'));
                $event->cover_image = $coverImageUrl;
            }

            if ($request->has('name')) {
                $event->name = $request->name;
            }

            if ($request->has('visibility')) {
                $event->visibility = $request->visibility;
            }

            $event->save();

            return $this->successResponse($event);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function destroy(Event $event)
    {
        try {
            $this->authorize('delete', $event);
            $event->delete();
            return $this->successResponse('Event deleted successfully');
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    protected function uploadImageToCloudinary($file)
    {
        $uploadedFileUrl = Cloudinary::uploadFile($file->getRealPath())->getSecurePath();
        return $uploadedFileUrl;
    }

    public function gallery(Event $event)
    {
        try {

            if ($event->visibility || $event->user_id == Auth::id()) {
                $media = $event->media;
                return response()->json($media);
            }

            return response()->json(['message' => 'This gallery is private.'], 403);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
