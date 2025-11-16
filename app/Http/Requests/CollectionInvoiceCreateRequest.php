<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectionInvoiceCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'string', 'max:64'],
            'contact_id' => ['required', 'string', 'max:64'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'max:255', 'email'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'amount_due' => ['required', 'numeric'],
            'amount_paid' => ['required', 'numeric'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
        ];
    }
}
