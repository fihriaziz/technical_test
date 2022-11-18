<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function showUsers()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => 200,
                'data' => $users,
                'message' => 'Get all user'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data user not found'
            ], 400);
        }
    }

    public function addUser(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => bcrypt($req->password),
                'role' => $req->role
            ]);

            if ($user) {
                return response()->json([
                    'status' => 201,
                    'data' => $user,
                    'message' => 'Create user success'
                ], 201);
            }

            return response()->json([
                'status' => 409,
                'message' => 'Failed to save data'
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $req, $id)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = User::findOrFail($id);

            if ($user) {
                $data = [
                    'name' => $req->name,
                    'email' => $req->email,
                    'password' => $user ? $user->password : bcrypt($req->password),
                    'role' => $req->role
                ];

                $user->update($data);

                return response()->json([
                    'status' => 200,
                    'message' => 'Update user success'
                ], 200);
            }

            return response()->json([
                'status' => 404,
                'message' => 'User not found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user) {
                $user->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Delete user success'
                ], 200);
            }

            return response()->json([
                'status' => 404,
                'message' => 'User not found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function changeAkses(Request $req, $id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user) {
                $user->update([
                    'role' => $req->role
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Update role success'
                ], 200);
            }

            return response()->json([
                'status' => 404,
                'message' => 'Role not found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
