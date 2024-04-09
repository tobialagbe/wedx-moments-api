<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Media;
use App\Comment;
use App\Like;
use App\Event;


class DashboardController extends Controller
{
    public function dashboardData(Request $request)
{
    try {
        $totalMedia = Media::count();
        $totalVideos = Media::where('media_type', 'video')->count();
        $totalImages = Media::where('media_type', 'image')->count();
        $totalComments = Comment::count();
        $totalLikes = Like::count();
        $totalEvents = Event::count();

        $dashboardData = [
            'total_media' => $totalMedia,
            'total_videos' => $totalVideos,
            'total_images' => $totalImages,
            'total_comments' => $totalComments,
            'total_likes' => $totalLikes,
            'total_events' => $totalEvents,
        ];

        return $this->successResponse($dashboardData);
    } catch (\Exception $e) {
        // return response()->json(['error' => $e->getMessage()], 500);
        return $this->exceptionResponse($e);
    }
}
}
