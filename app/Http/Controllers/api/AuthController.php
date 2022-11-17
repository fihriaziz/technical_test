<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => bcrypt($req->password),
                'role' => $req->role
            ]);

            if ($user) {
                return response()->json([
                    'data' => $user,
                    'status' => 201,
                ], 201);
            }

            return response()->json([
                'status' => 409,
                'message' => 'Register failed'
            ], 409);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 403);
            }

            $user = User::where('email', $req->email)->first();

            if ($user) {
                $token = $user->createToken('auth_access')->plainTextToken;
                $credentials = $req->only(['email', 'password']);
                if (Auth::attempt($credentials)) {
                    return response()->json([
                        'status' => 200,
                        'data' => $user,
                        'access_token' => $token,
                        'type' => 'Bearer'
                    ], 200);
                }
            }
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
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
                'status' => 200,
                'message' => 'Logout Success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function forgot_password(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $status = Password::sendResetLink($req->only('email'));

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

    public function reset_password(Request $req)
    {
        $validator = Validator::make($req->only(['token', 'password']), [
            'token' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credential = explode("#", base64_decode($req->token));
        $req->merge([
            'email' => $credential[0],
            'token' => $credential[1]
        ]);

        $status = Password::reset(
            $req->all(),
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
