<?php

namespace App\Http\Requests;

use App\Rules\OnlyOneIsCorrect;
use Illuminate\Foundation\Http\FormRequest;


class UpdateQuestionRequest extends FormRequest
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
            'question' => ['required'],
            'question.type' => ['required','sometimes', 'string', 'in:mcq,open_ended'],
            'question.text' => ['required','sometimes', 'string'],
            'question.image' => ['nullable', 'string', 'max:1024'],
            'question.options' => ['required_if:type,mcq', 'array', 'min:2', new OnlyOneIsCorrect()],
            'question.options.*.text' => ['required_if:type,mcq', 'string'],
            'question.options.*.image' => ['nullable', 'image', 'max:1024'],
            // there is a true option with value 1
            'question.options.*.is_correct' => ['required_if:type,mcq', 'boolean'],
            'question.answer' => ['required','sometimes', 'string'],
            'image' => ['nullable', 'image', 'max:1024'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'question' => json_decode($this->question, true),
        ]);
    }
}
