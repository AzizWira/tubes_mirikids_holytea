<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $data['username'])->first();

        // username / password salah
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah',
            ], 401);
        }

        // akun nonaktif
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif',
            ], 403);
        }

        // hapus token lama (1 admin = 1 sesi)
        $user->tokens()->delete();

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $request->user()->id,
                'username' => $request->user()->username,
                'role' => $request->user()->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // guard: kalau token tidak ada
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
