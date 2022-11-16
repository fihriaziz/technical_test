<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        try {
            $req->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role' => 'required'
            ]);

            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => bcrypt($req->password),
                'role' => $req->role
            ]);

            return response()->json([
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $req)
    {
        try {
            $req->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $req->email)->first();
            $token = $user->createToken('auth_access')->plainTextToken;

            $credentials = $req->only(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            return response()->json([
                'data' => $user,
                'access_token' => $token,
                'type' => 'Bearer'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message:' . $e->getMessage()]);
        }
    }

    public function me()
    {
        return Auth::user();
    }

    public function logout(Request $req)
    {
        try {
            Auth::user()->tokens()->where('id', Auth::user()->id)->delete();
            return response()->json([
                'message' => 'Logout Success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function forgot(Request $req)
    {
        try {
            $credentials = $req->validate([
                'email' => 'required|email'
            ]);
            $status = Password::sendResetLink($credentials);

            return $status = Password::RESET_LINK_SENT
                ? response()->json(['message' => 'Sent reset password'], 200)
                : back()->withErrors(['email' => $status]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function forgot_password($token)
    {
        if (!$token) {
            return response()->json([
                'message' => 'Not found'
            ], 404);
        }

        return response()->json([
            'token' => $token
        ]);
    }

    public function reset(Request $req, $id)
    {
        try {
            $req->validate([
                'password' => 'required'
            ]);

            $user = User::findOrFail($id);

            $user->update([
                'password' => bcrypt($req->password)
            ]);

            return response()->json([
                'message' => 'Password has been changed'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
