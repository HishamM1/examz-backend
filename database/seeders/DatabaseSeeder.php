<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Announcement;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Seeder;
use App\Models\User;
use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Database\Eloquent\Collection;
use Closure;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $students = User::factory(20)->create([
            'role'=> 'student',
        ]);

        $teachers = User::factory(5)->create([
            'role' => 'teacher',
        ]);

        $schools = ['school 1', 'school 2', 'school 3', 'school 4', 'school 5'];

        $students->each(function ($student) use ($schools) {
            $student->student()->create([
                'school' => $schools[array_rand($schools)],
            ]);
        });

        $subjects = ['subject 1', 'subject 2', 'subject 3', 'subject 4', 'subject 5'];

        $teachers->each(function ($teacher) use ($subjects) {
            $teacher->teacher()->create([
                'subject' => $subjects[array_rand($subjects)],
                'join_code' => $teacher->id . '-' . rand(1000, 9999),
            ]);
        });


        $teachers->each(function ($teacher) use ($students) {
            $students->random(5)->each(function ($student) use ($teacher) {
                $student->student->teachers()->attach($teacher->teacher->id);
            });
        });

        // create 5 exams for each teacher
        $teachers->each(function ($teacher) {
            $teacher->teacher->exams()->createMany(
                Exam::factory(3)->make()->toArray()
            );
        });

        // create 5 announcements for each teacher
        $teachers->each(function ($teacher) {
            $teacher->teacher->announcements()->createMany(
                Announcement::factory(5)->make()->toArray()
            );
        });

        // create 5 questions for each exam
        Exam::all()->each(function ($exam) {
            $exam->questions()->createMany(
                Question::factory(6)->make()->toArray()
            );
        });

        // create 5 options for each question of type mcq
        Question::where('type', 'mcq')->each(function ($question) {
            $question->options()->createMany([
                [
                    'text' => 'option 1',
                    'is_correct' => true,
                ],
                [
                    'text' => 'option 2',
                    'is_correct' => false,
                ],
                [
                    'text' => 'option 3',
                    'is_correct' => false,
                ],
                [
                    'text' => 'option 4',
                    'is_correct' => false,
                ],
            ]);

            $question->answer()->create([
                'exam_id' => $question->exam->id,
                'teacher_id' => $question->exam->teacher->id,
                'answer' => $question->options->where('is_correct', true)->first()->id,
            ]);
        });
        
        // create 5 answers for each question of type open_ended
        Question::where('type', 'open_ended')->each(function ($question) {
            $question->answer()->create([
                'exam_id' => $question->exam->id,
                'teacher_id' => $question->exam->teacher->id,
                'answer' => 'answer text',
            ]);
        });
    }
}
