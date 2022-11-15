<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        try {
            $user = User::where('email', $req->email)->first();
            $token = $user->createToken('auth_access')->plainTextToken;

            $credentials = $req->only(['email', 'password']);
            if (!$credentials) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'data' => $user,
                'access_token' => $token,
                'type' => 'Bearer'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message:' . $e->getMessage()]);
        }
    }
}
