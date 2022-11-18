<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRoleRequest;

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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 409,
                'message' => 'User failed to save'
            ], 409);
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found'
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found'
            ]);
        }
    }

    public function changeAkses($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role == 'Admin') {
                $user->update(['role' => 'User']);
            } else {
                $user->update(['role' => 'Admin']);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Update hak akses'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User not found'
            ]);
        }
    }
}
