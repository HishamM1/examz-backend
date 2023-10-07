<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Teacher;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Exam::class, 'exam');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $teacher_id = Teacher::select('id')->where('user_id', $user->id)->first()->id;
        $exams = Exam::select('id', 'teacher_id', 'subject', 'title', 'description', 'duration', 'start_time', 'end_time', 'image')->where('teacher_id', $teacher_id)->with('teacher.user:id,full_name')->filter($request->all(['search', 'active']))->latest()->fastPaginate(8);
        return ExamResource::collection($exams);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {

        return ExamResource::make($exam->load('teacher.user:id,full_name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $request->validated();

        $exam->update($request->all());

        return response()->json([
            'message' => 'Exam updated successfully',
            'data' => ExamResource::make($exam)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam, ImageService $imageService)
    {
        // if questions has images, delete them
        foreach ($exam->questions as $question) {
            if ($question->image) {
                $imageService->delete($question->image);
            }
        }

        $exam->questions()->delete();
        $exam->delete();
        return response()->noContent();
    }
}
