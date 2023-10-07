<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->full_name,
            'email' => $this->user->email,
            'phone_number' => $this->user->phone_number,
            'image' => $this->user->profile_picture,
            'school' => $this->school,
            'exams' => ExamResultsResource::collection($this->exams->load(['questions:id,exam_id,score,type', 'studentAnswers' => function ($query) {
                $query->select('exam_id', 'question_id', 'student_id', 'answer','score')->where('student_id', $this->id);
            }])),
        ];
    }
}
