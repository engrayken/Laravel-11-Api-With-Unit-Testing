<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            "name" => ["required", "regex:/^[a-zA-Z]{1}[a-zA-Z'-_]+/", "max:50"],
            "email" => ["required", "email", "max:200", "unique:p_users,email", "unique:users,email"],
            "phone" => ["required", "max:20","unique:p_users,phone", "unique:users,phone"],
            "password" => ["required",'regex:/[!@#$%^&*(),.?":{}|<>]/',"min:8", "max:32"],
        ];
    }
}
