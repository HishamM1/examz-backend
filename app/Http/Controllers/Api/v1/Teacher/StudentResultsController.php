<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentExamsResource;
use App\Models\Student;


class StudentResultsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Student $student)
    {
        $student = $student->load('exams','user');
        
        return StudentExamsResource::make($student);
    }
}
