<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherStudentsResource;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $teacher_id = Teacher::select('id')->where('user_id', $user->id)->first()->id;
        $students_ids = DB::table('teacher_student')->select('student_id')->where('teacher_id', $teacher_id)->pluck('student_id');
        
        return TeacherStudentsResource::collection(Student::select('id', 'user_id', 'school')->whereIn('id', $students_ids)->with('user:id,full_name,email,phone_number,about,profile_picture')->search($request->search)->fastPaginate(10));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $student_id)
    {
        $student = Student::findOrFail($student_id);
        $request->user()->teacher->students()->detach($student->id);
        return response()->json([
            'message' => 'Student deleted successfully'
        ], 200);
    }
}
