<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionsRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Exam;
use App\Models\Question;
use App\Http\Resources\QuestionsResource;
use App\Services\ImageService;

class QuestionController extends Controller
{
    public function index(Exam $exam)
    {
        $this->authorize('viewAny', [Question::class, $exam]);
        return QuestionsResource::collection($exam->questions->load('options', 'answer'));
    }
    public function show(Exam $exam, Question $question)
    {
        $this->authorize('view', [Question::class, $question]);
        return QuestionsResource::make($exam->questions()->findOrFail($question->id)->load('answer', 'options'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionsRequest $request, Exam $exam, ImageService $imageService)
    {
        $this->authorize('create', [Question::class, $exam]);

        $request->validated();
        $question = $request->question;

        if ($request->hasFile('image')) {
            $question['image'] = $imageService->upload($request->image);
        }

        $question_data = $exam->questions()->create([
            'type' => $question['type'],
            'text' => $question['text'],
            'image' => $question['image'] ?? null,
            'score' => $question['score'],
        ]);


        if ($question['type'] === "mcq") {
            foreach ($question['options'] as $key => $option) {
                unset($question['options'][$key]['id']);
            }
            $options = $question_data->options()->createMany($question['options']);
            $question_data->answer()->create([
                'teacher_id' => auth()->user()->teacher->id,
                'exam_id' => $exam->id,
                'answer' => $options->where('is_correct', true)->first()->id
            ]);
        } else {
            $question_data->answer()->create([
                'teacher_id' => auth()->user()->teacher->id,
                'exam_id' => $exam->id,
                'answer' => $question['answer']
            ]);
        }

        return response()->json([
            'message' => 'Question created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, string $exam, Question $question, ImageService $imageService)
    {
        $this->authorize('update', [Question::class, $question]);

        $request->validated();

        $question->update([
            'text' => $request->question['text'],
            'score' => $request->question['score'],
        ]);

        if ($question->isMCQ() && $request->question['options']) {
            $question->options()->delete();
            // remove ids from options
            foreach ($request->question['options'] as $option) {
                unset($option['id']);
            }
            $options = $question->options()->createMany($request->question['options']);
            $question->answer()->update([
                'answer' => $options->where('is_correct', 1)->first()->id
            ]);
        } elseif ($question->isOpenEnded() && $request->question['answer']) {
            $question->answer()->update([
                'answer' => $request->question['answer']
            ]);
        }

        if ($request->has('image')) {
            if ($question->image != null) {
                $imageService->update($request->image, 'question', $question->id);
            } else {
                $question->update([
                    'image' => $imageService->upload($request->image)
                ]);
            }
        }

        if ($question->image != null && $request->question['image'] == null) {
            unlink(public_path(substr($question->image, 22)));
            $question->update([
                'image' => null
            ]);
        }

        return response()->json([
            'message' => 'Question updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam, Question $question)
    {
        $this->authorize('delete', [Question::class, $question]);

        $question = $exam->questions()->findOrFail($question->id);

        if($question->image != null) {
            unlink(public_path(substr($question->image, 22)));
        }

        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully',
        ]);
    }
}
