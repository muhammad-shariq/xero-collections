<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemplateRequest extends FormRequest
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
            'name' => 'required|string|min:3',
            'email_name_from' => 'required|string|min:3',
            'from_email' => 'required|email',
            'email_subject' => 'required|string|min:5',
            'template' => 'required|string|min:10',
            'document' => $this->getValidationDocumentRule('document')
        ];
    }

    public function getValidationDocumentRule(string $key): string
    {
        if (request()->hasFile($key)) {
            return "required|file|mimes:docx";
        }
        return "required|string";
    }
}
