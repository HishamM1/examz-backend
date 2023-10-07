<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamStudentsResource extends JsonResource
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
            'profile_picture' => $this->user->profile_picture,
            'total_score' => $this->pivot ? $this->pivot->total_score : 0,
            'status' => $this->pivot ? $this->pivot->status : null,
            'school' => $this->school,
        ];
    }
}
