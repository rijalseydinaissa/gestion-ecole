<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRole;
use Illuminate\Validation\Rules\Enum;

class CreateUserRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'fonction' => 'required|string',
            'email' => 'required|email|unique:users',
            'photo' => 'nullable|string', // URL de la photo
            'statut' => 'required|in:Bloquer,Actif',
            'role' => ['required', new Enum(UserRole::class)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'adresse.required' => 'L\'adresse est obligatoire',
            'telephone.required' => 'Le téléphone est obligatoire',
            'fonction.required' => 'La fonction est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'L\'email est déjà utilisé',
            'photo.required' => 'L\'URL de la photo est obligatoire',
            'statut.required' => 'Le statut est obligatoire',
            'role.required' => 'Le rôle est obligatoire',
            'role.*' => 'Le rôle sélectionné n\'est pas valide',
        ];
    }
}