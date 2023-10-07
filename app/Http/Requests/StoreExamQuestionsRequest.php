<?php

namespace App\Http\Requests;

use App\Rules\OnlyOneIsCorrect;
use Illuminate\Foundation\Http\FormRequest;

class StoreExamQuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'exam' => ['required', 'array'],
            'exam.title' => ['required', 'string', 'max:255'],
            'exam.description' => ['nullable', 'string'],
            'exam.start_time' => ['required', 'date', 'after_or_equal:today'],
            'exam.end_time' => ['nullable', 'date', 'after:exam.start_time'],
            'exam.duration' => ['required', 'numeric', 'min:1'],
            'exam.show_result' => ['required', 'boolean'],
            'exam.visible' => ['required', 'boolean'],
            'questions' => ['required', 'array'],
            'questions.*.type' => ['required', 'string', 'in:mcq,open_ended'],
            'questions.*.text' => ['required', 'string'],
            'questions.*.options' => ['required_if:questions.*.type,mcq', 'array', 'min:2', new OnlyOneIsCorrect()],
            'questions.*.options.*.text' => ['required', 'string'],
            'questions.*.options.*.is_correct' => ['required', 'boolean'],
            'questions.*.answer' => ['required_if:questions.*.type,open_ended', 'string'],
            'questions.*.score' => ['required', 'numeric', 'min:1'],
            'images' => ['nullable', 'array', 'min:1'],
            'images.*' => ['nullable', 'image'],
        ];
    }

    public function prepareForValidation() {
        $questions = $this->questions;
        foreach ($questions as $key => $question) {
            $questions[$key] = json_decode($question, true);
        }
        $this->merge([
            'questions' => $questions,
            'exam' => json_decode($this->exam, true),
        ]);
    }

    public function messages() {
        return [
            'exam.start_time.after_or_equal' => 'The start time must be after or equal today.',
            'exam.end_time.after' => 'The end time must be after start time.',
            'exam.duration.min' => 'The duration must be at least 1 minute.',
            'exam.title.max' => 'The title may not be greater than 255 characters.',
            'exam.title.required' => 'The title field is required.',
            'exam.duration.required' => 'The duration field is required.',
            'exam.start_time.required' => 'The start time field is required.',
            'questions.*.options.required_if' => 'The options field is required.',
            'questions.*.answer.required_if' => 'The answer field is required.',
            'questions.*.text.required' => 'The text field is required.',
            'questions.*.score.min' => 'The score must be at least 1.',
            'questions.*.options.min' => 'The options must be at least 2.',
            'questions.*.score.required' => 'The score field is required.',
            'question.*.text.required' => 'The text field is required.',
            'questions.*.options.*.text.required' => 'The option text field is required.',
        ];
    }
}
