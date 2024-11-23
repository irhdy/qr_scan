<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // validasi untuk username dan password wajib diisi
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ]
        );

        // jika gagal maka mengembalikan format json, yg isinya sebagai berikut
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
                'data' => []
            ]);
        }

        // cari ke table User, pada record pertama, berdasarkan kolom username
        $user = User::where('username', $request->username)->first();

        // Verify user existence and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid username or password',
            ]);
        }

        // Generate an authentication token if credentials are valid
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return success response with user details and token
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'id' => $user->id,
                'name' => $user->nama,
                'token' => $token
            ]
        ], 200);
    }

    public function logout (Request $request){

        $user = $request->user();

        if($user){

            $user->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message'=> 'logout berhasil',
            ], 200);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'user tidak ditemukan atau sudah logout',

            ], 400);
        }

    }


}