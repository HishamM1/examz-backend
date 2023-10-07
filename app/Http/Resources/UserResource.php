<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'about' => $this->about,
            'profile_picture' => $this->profile_picture,
            'role' => $this->role,
            'verified' => $this->email_verified_at != null,
            'subject' => $this->when($user->isTeacher(), $this->teacher?->subject),
            'teacher_id' => $this->when($user->isTeacher(), $this->teacher?->id),
            'join_code' => $this->when($user->isTeacher(), $this->teacher?->join_code),
            'school' => $this->when($user->isStudent(), $this->student?->school),
            'student_id' => $this->when($user->isStudent(), $this->student?->id),
            'likes' => $this->when($user->isStudent(), $this->student?->likes()->pluck('announcement_id')),
            'views' => $this->when($user->isStudent(), $this->student?->views()->pluck('announcement_id')),
        ];
    }
}
