<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\UserLocal;
use Illuminate\Support\Facades\Auth;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    public function authenticate(Request $request)
    {
        // Valider les identifiants
        $credentials = $request->only('email', 'password');

        // Tenter de connecter l'utilisateur avec les identifiants fournis
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Créer un token Passport pour l'utilisateur
        $token = $user->createToken('AccessToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // Supprimer le token actuel
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
