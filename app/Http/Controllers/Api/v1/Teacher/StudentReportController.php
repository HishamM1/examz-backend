<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\StudentExamsResource;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Student $student)
    {
        $fastapi_url = config('app.fastapi_url');

        // check if student has exams
        if($student->exams->count() === 0){
            return response()->json(['message' => 'Student has no exams'], 404);
        }

        $response = Http::retry(0)->get("$fastapi_url/student/report", ["data" => StudentExamsResource::make($student->load('exams', 'user'))->toJson()]);
        
        if($response->status() === 200){
            Storage::disk('public')->put('reports/'.$student->id.'.pdf', $response->body());
            return response()->download(storage_path('app/public/reports/'.$student->id.'.pdf'));
        }

        return response()->json(['message' => 'Something went wrong'], 500);

    }
}
