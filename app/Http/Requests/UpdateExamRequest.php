<?php

namespace App\Http\Requests;

use App\Rules\BeforeEndTimeIfExists;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateExamRequest extends FormRequest
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
            'title' => ['required','sometimes', 'string', 'max:255'],
            'description' => ['required','sometimes', 'string'],
            'start_time' => ['required','sometimes', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'duration' => ['required','sometimes', 'numeric', 'min:1'],
            'show_result' => ['required','sometimes', 'boolean'],
            'visible' => ['required','sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'start_time' => Carbon::parse($this->start_time)->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time ? Carbon::parse($this->end_time)->format('Y-m-d H:i:s') : null,
        ]);
    }
}
