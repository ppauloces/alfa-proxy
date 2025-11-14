<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email:rfc,dns|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser um e-mail válido.',
            'email.unique' => 'O e-mail já está em uso.',
            'username.required' => 'O username é obrigatório.',
            'username.unique' => 'O username já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
            'password_confirmation.same' => 'A confirmação de senha deve ser igual à senha.',
        ];
    }
}
