<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends ApiRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',
            'email' => 'required|max:255|unique:users',
            'password' => ['required', 'confirmed', 'max:50',
                Password::min(config('auth.min_password_length') ?: 8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()],
            'terms_agreed' => 'required|boolean|in:1,true',
        ];
    }
}
