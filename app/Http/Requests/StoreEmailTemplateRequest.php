<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreEmailTemplateRequest extends FormRequest
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
            'template' => 'required|integer|min:1',
            'days' => 'required|integer|min: 1',
            'user_id' => 'required|integer|min:1'
        ];
    }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'status' => 1,
    //         'data' => [
    //             "headers" => [],
    //             "original" => [
    //                 "error" => $validator->errors(),
    //             ],
    //             "exception" => null
    //         ],
    //     ], Response::HTTP_UNPROCESSABLE_ENTITY));
    // }
}
