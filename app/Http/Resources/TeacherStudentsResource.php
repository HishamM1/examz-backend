<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherStudentsResource extends JsonResource
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
            'phone' => $this->user->phone_number,
            'image' => $this->user->profile_picture,
            'about' => $this->user->about,
            'school' => $this->school,
        ];
    }
}
