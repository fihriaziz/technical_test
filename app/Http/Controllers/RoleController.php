<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function showUsers()
    {
        try {
            $users = User::all();
            return response()->json([
                'data' => $users,
                'message' => 'Get all user'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data not found'
            ]);
        }
    }

    public function addUser(Request $req)
    {
        try {
            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => bcrypt($req->password),
                'role' => $req->role
            ]);

            return response()->json([
                'data' => $user,
                'message' => 'Create user successully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $req, $id)
    {
        try {
            $user = User::findOrFail($id);

            $data = [
                'name' => $req->name,
                'email' => $req->email
            ];

            $user->update($data);

            return response()->json([
                'message' => 'Update user successfull'
            ], 201);
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
            $user->delete();

            return response()->json([
                'message' => 'Delete user successfull'
            ], 200);
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
            $user->update([
                'role' => $req->role
            ]);

            return response()->json([
                'message' => 'Update hak akses'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}