<?php

namespace App\Http\Requests;

use App\Rules\OnlyOneIsCorrect;
use Illuminate\Foundation\Http\FormRequest;


class StoreQuestionsRequest extends FormRequest
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
            'image' => ['nullable', 'image'],
            'question' => ['required'],
            'question.type' => ['required', 'string', 'in:mcq,open_ended'],
            'question.text' => ['required', 'string'],
            'question.options' => ['required_if:question.type,mcq', 'array', 'min:2', new OnlyOneIsCorrect() ],
            'question.options.*.text' => ['required', 'string'],
            'question.options.*.is_correct' => ['required', 'boolean'],
            'question.answer' => ['required_if:question.type,open_ended', 'string'],
            'question.score' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function prepareForValidation() {
        $this->merge([
            'question' => json_decode($this->question, true),
        ]);
    }

    public function messages() {
        return [
            'question.options.required_if' => 'The options field is required.',
            'question.answer.required_if' => 'The answer field is required.',
            'question.options.min' => 'The options field must have at least 2 items.',
            'question.options.*.is_correct' => 'Only one option can be correct.',
            'question.options.*.text.required' => 'The option text field is required.',
            'question.score.required' => 'The score field is required.',
            'question.score.min' => 'The score field must be at least 1.',
            'question.type.required' => 'The type field is required.',
            'question.type.in' => 'The selected type is invalid.',
            'question.text.required' => 'The text field is required.',
            'image.image' => 'The image must be an image.',
        ];
    }


}
