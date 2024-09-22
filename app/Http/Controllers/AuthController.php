<?php

namespace App\Http\Controllers;

use App\Services\AuthentificationServiceInterface;
use Illuminate\Http\Request;
use App\Services\AuthenticationFirebase;
use App\Services\AuthentificationPassport;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $provider = $request->input('issa');  // Cela peut être 'firebase' ou 'passport'

        if ($provider == 'firebase') {
            $this->authService = app()->make(AuthenticationFirebase::class);
        } else {
            $this->authService = app()->make(AuthentificationPassport::class);
        }

        return $this->authService->authenticate($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}
