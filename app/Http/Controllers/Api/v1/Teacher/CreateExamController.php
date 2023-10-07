<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Events\ExamCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamQuestionsRequest;
use App\Models\Exam;
use App\Notifications\NewExam;
use App\Services\ImageService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;


class CreateExamController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreExamQuestionsRequest $request, ImageService $imageService)
    {
        $request->validated();

        $teacher = auth()->user()->teacher;

        $exam = Exam::create([
            'teacher_id' => $teacher->id,
            'subject' => $request->exam['subject'] ?? $teacher->subject,
            'title' => $request->exam['title'],
            'description' => $request->exam['description'],
            'start_time' => $request->exam['start_time'],
            'end_time' => $request->exam['end_time'],
            'duration' => $request->exam['duration'],
            'show_result' => $request->exam['show_result'],
            'visible' => $request->exam['visible'],
        ]);

        $questions_images = [];

        for ($i = 1; $i <= count($request->questions); $i++) {
            $questions_images[] = [
                'question' => $request->questions[$i],
                'image' => $request->images[$i] ?? null,
            ];
        }

        foreach ($questions_images as $question) {
            if (is_file($question['image'])) {
                $question['image'] = $imageService->upload($question['image']);
            }
            unset($question['question']['image_path']);

            $question_data = $exam->questions()->create([
                'type' => $question['question']['type'],
                'text' => $question['question']['text'],
                'image' => $question['image'] ?? null,
                'score' => $question['question']['score'],
            ]);

            if ($question['question']['type'] === 'mcq') {

                // remove id from options
                foreach ($question['question']['options'] as $key => $option) {
                    unset($question['question']['options'][$key]['id']);
                }

                $options = $question_data->options()->createMany($question['question']['options']);
                $question_data->answer()->create([
                    'teacher_id' => auth()->user()->teacher->id,
                    'exam_id' => $exam->id,
                    'answer' => $options->where('is_correct', true)->first()->id
                ]);
            } else {
                $question_data->answer()->create([
                    'teacher_id' => auth()->user()->teacher->id,
                    'exam_id' => $exam->id,
                    'answer' => $question['question']['answer']
                ]);
            }

            if ($exam->visible && $exam->start_time <= now()) {
                $students = auth()->user()->teacher->load('students.user')->students->pluck('user');
                ExamCreated::dispatch($students, $exam, $request->user()->full_name);
            }
        }

        return response()->json([
            'message' => 'Exam created successfully',
        ], 201);
    }
}
