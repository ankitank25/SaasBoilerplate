<?php

namespace App\Http\Requests\Api\Page;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'title' => 'required',
            'url_key' => 'required|max:100',
            'show_title' => '',
            'content' => '',
            'layout' => '',
            'meta_title' => '',
            'meta_description' => '',
            'status' => '',
        ];
    }
}
