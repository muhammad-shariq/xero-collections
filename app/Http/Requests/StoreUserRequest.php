<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\UserService;

class StoreUserRequest extends FormRequest
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
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
            'email' => $this->getValidationEmailRule('email'),
            'role_id' => 'required|integer|min:1',
            'password' => 'required|string|min:3',
        ];
    }

    public function getValidationEmailRule(string $key): string
    {
        if (request()->has([$key, 'id'])) {
            $email = request()->email;
            $id = request()->id;
            $data = $this->userService->findExistEmailValidate($email,$id);
            if( count($data) > 0 ) //email already taken by another user
                return "unique:App\Models\User,email";
            return "required|email"; //email available
        }
        return "required|email|unique:App\Models\User,email";
    }
}
