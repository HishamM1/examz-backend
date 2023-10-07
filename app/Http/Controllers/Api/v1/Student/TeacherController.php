<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentTeachersResource;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return StudentTeachersResource::collection($request->user()->student->teachers()->with('user:id,full_name,email,profile_picture,about,phone_number')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $join_code = $request->code;
        $teacher = Teacher::where('join_code', $join_code)->first();
        
        if(!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        if($request->user()->student->teachers->contains($teacher)) {
            return response()->json([
                'message' => 'You already joined to this teacher'
            ], 400);
        }

        $request->user()->student->teachers()->attach($teacher);

        return response()->json([
            'message' => 'Teacher joined successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $teacher_id)
    {
        $request->user()->student->teachers()->detach($teacher_id);
        return response()->json([
            'message' => 'Teacher deleted successfully'
        ], 200);
    }
}
