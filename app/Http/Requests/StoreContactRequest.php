<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'contact_id' => ['required', 'string', 'max:64'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'max:255', 'email'],
            'mobile_number' => ['nullable', 'regex:/(0)[0-9]/', 'not_regex:/[a-z]/']
        ];
    }
}
