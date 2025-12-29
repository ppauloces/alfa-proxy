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
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email|max:255',
            'username' => 'required|string|min:3|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome completo é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.unique' => 'Este e-mail já está cadastrado. Tente fazer login ou use outro e-mail.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',

            'username.required' => 'O username é obrigatório.',
            'username.min' => 'O username deve ter no mínimo 3 caracteres.',
            'username.max' => 'O username não pode ter mais de 50 caracteres.',
            'username.unique' => 'Este username já está em uso. Escolha outro.',
            'username.alpha_dash' => 'O username só pode conter letras, números, traços e underscores.',

            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A senha não pode ter mais de 255 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula e um número.',

            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
            'password_confirmation.same' => 'As senhas não coincidem. Digite a mesma senha nos dois campos.',
        ];
    }
}
