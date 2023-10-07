<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'subject' => $this->subject,
            'duration' => $this->duration,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'visible' => $this->visible,
            'show_result' => $this->show_result,
            'teacher_name' => $this->when($request->user()->isStudent(), $this->teacher->user->full_name),
            'status' => $this->when($request->user()->isStudent(), $this->status ?? false),
        ];
    }
}
