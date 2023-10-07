<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Events\AnnouncementCreated;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Notification;


class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->isTeacher()) {
            return Announcement::where('teacher_id', $user->teacher->id)->withCount('likes', 'views')->latest()->orderBy('id')->cursorPaginate(5);
        }

        $teachers = $user->student->teachers()->pluck('teacher_id')->toArray();
        return Announcement::whereIn('teacher_id', $teachers)->with('teacher.user')->withCount('likes', 'views')->latest()->orderBy('id')->cursorPaginate(5);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ImageService $image_service)
    {
        $request->validate([
            'text' => ['present', 'string', 'nullable'],
            'media' => ['required_if:text,=,null', 'file', 'mimes:jpg,jpeg,png,mp4,mov,avi,webm'],
        ]);

        $request->user()->teacher->announcements()->create([
            'text' => $request->text,
            'media' => $request->hasFile('media') ? $image_service->upload($request->media) : null,
        ]);

        $students = $request->user()->teacher->load('students.user')->students->pluck('user');

        AnnouncementCreated::dispatch($students, $request->user()->full_name);

        return response()->json([
            'message' => 'Announcement created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement, ImageService $image_service)
    {
        $request->validate([
            'text' => ['present', 'string', 'nullable'],
            'media' => ['file', 'mimes:jpg,jpeg,png,mp4,mov,avi,webm', 'nullable'],
        ]);

        $announcement->update([
            'text' => $request->text,
            'media' => $request->hasFile('media') ? $image_service->update($request->media, 'announcement', $announcement->id) : $announcement->media,
        ]);

        return response()->json([
            'message' => 'Announcement updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return response()->json([
            'message' => 'Announcement deleted successfully',
        ], 200);
    }
}
