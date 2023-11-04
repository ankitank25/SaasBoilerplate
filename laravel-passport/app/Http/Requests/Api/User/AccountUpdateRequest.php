<?php

namespace App\Http\Requests\Api\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AccountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $profile = User::firstWhere('uuid', $this->route('uuid'));

        return $profile && $this->user()->can('update', $profile);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|max:200',
            'last_name' => 'sometimes|required|max:200',
            'twofa_type' => 'sometimes|required|max:10',
            'meta' => 'sometimes|required|array',
            'email' => 'sometimes|required|max:255|unique:users',
            'password' => ['sometimes', 'required', 'confirmed', 'max:50',
                Password::min(config('auth.min_password_length') ?: 8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()],
            'role' => 'sometimes|required',
            'status' => 'sometimes|required|in:1,2,3',
        ];
    }
}
