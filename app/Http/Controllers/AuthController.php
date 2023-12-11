<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            "required" => "o campo :attribute é obrigatório"
        ]);

        $credentials = $request->only(["email", "password"]);

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        return $this->jsonResponse($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function refresh()
    {
        return $this->jsonResponse(auth()->refresh());
    }


    protected function jsonResponse($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
