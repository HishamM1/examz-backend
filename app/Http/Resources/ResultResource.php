<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    public static $wrap = 'user';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['question']['id'],
            'text' => $this['question']['text'],
            'image' => $this['question']['image'],
            'type' => $this['type'],
            'score' => $this['score'],
            'student_score' => $this['student_score'],
            'options' => OptionsResource::collection($this['question']['options']),
            'correct_answer' => $this['correct_answer'],
            'student_answer' => $this['student_answer'],
            'is_correct' => $this['is_correct'],
            'similarity' => $this['similarity'],
        ];
    }
}
