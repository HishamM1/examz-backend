<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->foreignId('exam_id');
            $table->foreignId('student_id');
            $table->foreignId('question_id');
            $table->string('answer');
            $table->float('similarity')->nullable();
            $table->float('score')->default(0);
            $table->primary(['student_id', 'question_id', 'exam_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
