<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Đăng ký
     // ===== ĐĂNG KÝ =====
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

    try {
        // 1. Tạo user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // mặc định
        ]);

        // 2. Tạo customer
        Customer::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'phone' => $request->phone,
            'registration_date' => now(),
        ]);

        // 3. Tạo token (optional)
        $token = $user->createToken('auth_token')->plainTextToken;

        DB::commit();

        return response()->json([
            'message' => 'Đăng ký thành công',
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Đăng ký thất bại',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    // ===== ĐĂNG NHẬP =====
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'data' => $user,
            'token' => $token
        ]);
    }

    // // ===== ĐĂNG XUẤT =====
    // public function logout()
    // {
    //     auth()->user()->tokens()->delete();

    //     return response()->json([
    //         'message' => 'Đăng xuất thành công'
    //     ]);
    // }
}
