<?php

namespace App\Services;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class AuthenticationFirebase implements AuthentificationServiceInterface
{
    protected $auth;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database_url'));
        $this->auth = $firebase->createAuth();
    }

    public function authenticate(Request $request)
    {
        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($request->email, $request->password);
            $token = $signInResult->idToken();
            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        // Firebase doesn't have a server-side logout, 
        // but we can invalidate the token on the client side
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
