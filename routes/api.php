<?php

use App\Http\Controllers\Api\v1\Auth\ChangePasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\Auth\LogoutController;
use App\Http\Controllers\Api\v1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\v1\Auth\DeleteUserController;
use App\Http\Controllers\Api\v1\Auth\NotificationController;
use App\Http\Controllers\Api\v1\Auth\ProfileController;
use App\Http\Controllers\Api\v1\Auth\UpdateProfileController;

use App\Http\Controllers\Api\v1\Teacher\ExamController;
use App\Http\Controllers\Api\v1\Teacher\QuestionController;
use App\Http\Controllers\Api\v1\Teacher\ExamStudentsController;
use App\Http\Controllers\Api\v1\Teacher\UpdateQuestionScoreController;
use App\Http\Controllers\Api\v1\Teacher\StudentController;
use App\Http\Controllers\Api\v1\Teacher\AnnouncementController;
use App\Http\Controllers\Api\v1\Teacher\StudentResultsController;
use App\Http\Controllers\Api\v1\Teacher\CreateExamController;
use App\Http\Controllers\Api\v1\Teacher\StudentReportController;



use App\Http\Controllers\Api\v1\Student\AnswerExamController;
use App\Http\Controllers\Api\v1\Student\StudentTeachersExamsController;
use App\Http\Controllers\Api\v1\Student\StartExamController;
use App\Http\Controllers\Api\v1\Student\TeacherController;
use App\Http\Controllers\Api\v1\Student\ViewAnnouncementController;
use App\Http\Controllers\Api\v1\Student\LikeAnnouncementController;
use App\Http\Controllers\Api\v1\Student\ExamInProgressController;
use App\Http\Controllers\Api\v1\UploadImageController;

// Auth routes
Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::get('/user', ProfileController::class);

    Route::put('/user', UpdateProfileController::class);

    Route::post('/logout', LogoutController::class);

    Route::delete('/delete_user', DeleteUserController::class);

    Route::post('/image/{model}/{modelId}', UploadImageController::class);

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify');

    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])->name('verification.send');

    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::post('/notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead']);

    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll']);

    Route::post('/change-password', ChangePasswordController::class);
});



// Teacher routes
Route::middleware(['auth:sanctum','teacher'])->prefix('teacher')->group(function () {

    Route::apiResource('exams', ExamController::class)->except(['store']);

    Route::apiResource('exams.questions', QuestionController::class)->except(['update']);
    
    Route::apiResource('exams.students', ExamStudentsController::class)->only(['index', 'show','destroy']);
    
    Route::apiResource('students', StudentController::class)->only(['index', 'destroy']);
    
    Route::apiResource('announcements', AnnouncementController::class)->withoutMiddleware(['teacher'])->except('update');

    Route::get('/students/{student}/results', StudentResultsController::class);

    Route::post('/exams', CreateExamController::class);

    Route::post('/exams/{exam}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');

    Route::post('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');

    Route::put('/exams/{exam}/students/{student}/question/{question}', UpdateQuestionScoreController::class);

    Route::get('/students/{student}/report', StudentReportController::class);
});

// Student routes
Route::middleware(['auth:sanctum','student'])->prefix('student')->group(function () {

    // Start an exam
    Route::put('/exams/{exam}', StartExamController::class);
    
    // Student answer an exam
    Route::post('/exams/{exam}', AnswerExamController::class);

    // Show all exams of student's teachers
    Route::get('/exams', StudentTeachersExamsController::class);

    Route::apiResource('teachers', TeacherController::class)->only(['index', 'store', 'destroy']);

    // Show student result of an exam
    // Route::apiResource('exams.students', ExamStudentsController::class)->only(['show']);
    Route::get('/exams/{exam}/students/{student}', [ExamStudentsController::class, 'show']);

    // View announcement
    Route::put('/announcements/{id}', ViewAnnouncementController::class);

    // Like announcement
    Route::put('/announcements/{id}/like', [LikeAnnouncementController::class, 'like']);

    // Dislike announcement
    Route::put('/announcements/{id}/dislike', [LikeAnnouncementController::class, 'dislike']);

    // Show exam in progress
    Route::get('/exams/in_progress', ExamInProgressController::class);
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);