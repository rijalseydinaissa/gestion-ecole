<?php

namespace App\Services;

use Illuminate\Http\Request;

interface FirebaseAuthenticationInterface
{
    public function authenticate(Request $request);
    public function logout(Request $request);
}
