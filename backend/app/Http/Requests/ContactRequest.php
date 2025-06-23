<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Your name is required.',
            'email.required' => 'Your email is required.',
            'email.email' => 'Please provide a valid email address.',
            'message.required' => 'A message is required.',
        ];
    }
}
