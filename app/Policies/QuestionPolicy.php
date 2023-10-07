<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Exam $exam): bool
    {
        return $user->isTeacher() && $user->teacher->id == $exam->teacher_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Question $question): bool
    {
        return $user->isTeacher() && $user->teacher->id == $question->exam->teacher_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Exam $exam): bool
    {
        return $user->isTeacher() && $user->teacher->id == $exam->teacher_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        return $user->isTeacher() && $user->teacher->id == $question->exam->teacher_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->isTeacher() && $user->teacher->id == $question->exam->teacher_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Question $question): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Question $question): bool
    // {
    //     //
    // }
}
