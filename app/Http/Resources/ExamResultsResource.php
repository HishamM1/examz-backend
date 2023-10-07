<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mcq_questions_ids = $this->questions->where('type', 'mcq')->pluck('id')->toArray();
        $open_ended_questions_ids = $this->questions->where('type', 'open_ended')->pluck('id')->toArray();

        $answered_mcq = $this->studentAnswers->whereIn('question_id', $mcq_questions_ids)->where('answer', '!=', null)->count();
        $answered_open_ended = $this->studentAnswers->whereIn('question_id', $open_ended_questions_ids)->where('answer', '!=', null)->count();

        $wrong_mcq = $this->studentAnswers->whereIn('question_id', $mcq_questions_ids)->where('score', 0)->count();
        $wrong_open_ended = $this->studentAnswers->whereIn('question_id', $open_ended_questions_ids)->where('score', 0)->count();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'subject' => $this->subject,
            'duration' => $this->duration,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'taken_at' => $this->pivot->created_at,
            'student_score' => $this->pivot->total_score ?? 0,
            'total_score' => $this->questions->sum('score'),
            'score_percentage' => $this->pivot->total_score ? round((($this->pivot->total_score / $this->questions->sum('score')) * 100),2) : 0,
            'status' => $this->pivot->status,
            'questions_count' => $this->questions->count(),
            'answered_questions_count' => $this->studentAnswers->where('answer','!=' ,null)->count(),
            'open_ended_questions_count' => $this->questions->where('type', 'open_ended')->count(),
            'answered_open_ended_count' => $this->when($this->questions->where('type', 'open_ended')->count() > 0, $answered_open_ended),
            'mcq_questions_count' => $this->questions->where('type', 'mcq')->count(),
            'answered_mcq_count' =>  $this->when($this->questions->where('type', 'mcq')->count() > 0, $answered_mcq),
            'wrong_mcq_count' => $this->when($this->questions->where('type', 'mcq')->count() > 0, $wrong_mcq),
            'wrong_open_ended_count' => $this->when($this->questions->where('type', 'open_ended')->count() > 0, $wrong_open_ended),
        ];
    }
}
