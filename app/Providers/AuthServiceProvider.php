<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\Exam;
use App\Policies\ExamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Exam::class => ExamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-results', function (User $user, string $exam_id) {
            $exam = Exam::findOrFail($exam_id);
            return ($user->isStudent() && $exam->students->contains($user->student)) || ($user->isTeacher() && $exam->teacher_id === $user->teacher->id);
        });
    }
}
