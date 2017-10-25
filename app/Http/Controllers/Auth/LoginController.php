<?php

namespace App\Http\Controllers\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.refresh', ['only' => ['refreshToken']]);
    }

    public function login(LoginRequest $request)
    {
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt([
                'email' => $request->get('email'),
                'password' => $request->get('password')
            ])) {
                return response(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response(
            compact('token'),
            200,
            ['authorization' => 'Bearer ' . $token . '']
        );
    }

    public function refreshToken()
    {
        return response(['token' => JWTAuth::getToken()]);
    }
}
