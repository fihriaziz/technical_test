<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        try {
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
            $user = User::where('email', $req->email)->first();
            $token = $user->createToken('auth_access')->plainTextToken;

            $credentials = $req->only(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
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

    public function logout()
    {
        try {
            auth()->logout;
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
            $credentials = $this->validate($req, [
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

    public function reset(Request $req, $id)
    {
        try {
            $this->validate($req, [
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
