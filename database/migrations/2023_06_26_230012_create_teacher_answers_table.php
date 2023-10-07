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
        Schema::create('teacher_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id');
            $table->foreignId('teacher_id');
            $table->foreignId('question_id');
            $table->string('answer');
            $table->unique(['exam_id', 'teacher_id', 'question_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_answers');
    }
};
