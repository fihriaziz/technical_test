<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function showUsers()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => 201,
                'data' => $users,
                'message' => 'Get all user'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data not found'
            ], 400);
        }
    }

    public function addUser(RegisterRequest $req)
    {
        try {
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
                    'message' => 'Create user successully'
                ], 201);
            }

            return response()->json([
                'status' => 409,
                'message' => 'User failed to save'
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(UpdateRoleRequest $req, $id)
    {
        try {
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
                    'message' => 'Update user successfull'
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
                    'message' => 'Delete user successfull'
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
            $validator = Validator::make($req->role, [
                'role' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = User::findOrFail($id);
            if ($user) {
                $user->update([
                    'role' => $req->role
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Update hak akses'
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
