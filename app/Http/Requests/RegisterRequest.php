<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'full_name' => ['required', 'string','max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required','string','numeric','min:10', 'unique:users,phone_number'],
            'password' => ['required', 'string', 'confirmed'],
            'role' => ['required','string','in:student,teacher'],
            'subject' => ['required_if:role,teacher','nullable' ,'string','max:255'],
            'school' => ['required_if:role,student','nullable' ,'string','max:255'],
            'about' => ['nullable','string','max:255'],
        ];
    }
}
