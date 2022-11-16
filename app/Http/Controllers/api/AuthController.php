<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function logout()
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

    public function forgot_password(Request $req)
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

    public function reset_token($token)
    {
        return $token;
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required',
        ]);

        $credential = explode("#", base64_decode($request->token));
        $request->merge([
            'email' => $credential[0],
            'token' => $credential[1]
        ]);

        $status = Password::reset(
            $request->all(),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status;

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
