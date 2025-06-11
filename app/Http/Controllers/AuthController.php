<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users,email',
            'umur_kandungan' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Registrasi gagal. Data tidak valid.',
                'errors' => $validator->errors(),
                'status' => 'fail'
            ], 422);
        }

        try {
            $user = User::create([
                'user_id' => \Illuminate\Support\Str::uuid()->toString(),
                'username' => $request->username,
                'email' => $request->email,
                'umur_kandungan' => $request->umur_kandungan,
                'password' => Hash::make($request->password), 
            ]);

            return response()->json([
                'message' => 'User berhasil didaftarkan.',
                'user' => $user,
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            Log::error("Registration Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat registrasi.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Login gagal. Data tidak valid.',
                    'errors' => $validator->errors(),
                    'status' => 'fail'
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $apiToken = Str::random(60);  
                $user->api_token = $apiToken;
                $user->save();

                // Set api_token ke cookies (httpOnly)
                return response()->json([
                    'message' => 'Login berhasil.',
                    'user' => $user,
                    'api_token' => $apiToken,
                    'status' => 'success'
                ], 200)->cookie('api_token', $apiToken, 60 * 24 * 7, null, null, false, true); // 7 hari, httpOnly
            }

            return response()->json([
                'message' => 'Email atau password salah.',
                'status' => 'fail'
            ], 401);
        } catch (\Exception $e) {
            Log::error("Login Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat login.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        $apiToken = $request->query('api_token');

        if (!$apiToken) {
            return response()->json([
                'message' => 'API token wajib diisi.',
                'status' => 'fail'
            ], 400);
        }

        $user = User::where('api_token', $apiToken)->first();

        if ($user) {
            $user->api_token = null;
            $user->save();

            return response()->json([
                'message' => 'Logout berhasil.',
                'status' => 'success'
            ], 200);
        }

        return response()->json([
            'message' => 'API token tidak valid.',
            'status' => 'fail'
        ], 401);
    }


    public function currentUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return response()->json([
                'user' => $user,
                'status' => 'success'
            ], 200);
        }

        return response()->json([
            'message' => 'User tidak ditemukan atau token tidak valid.',
            'status' => 'fail'
        ], 401);
    }

    public function changeFamilyRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'family_role' => 'required|string|in:ibu,ayah,user,admin',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan.',
            'family_role.required' => 'Family role wajib diisi.',
            'family_role.in' => 'Family role harus salah satu dari: ibu, ayah, user, admin.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Gagal mengubah role. Data tidak valid.',
                'errors' => $validator->errors(),
                'status' => 'fail'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.',
                'status' => 'fail'
            ], 404);
        }

        $user->family_role = $request->family_role;
        $user->save();

        return response()->json([
            'message' => 'Family role berhasil diubah.',
            'user' => $user,
            'status' => 'success'
        ], 200);
    }

    public function pairFamily(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|uuid|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors(),
                'status' => 'fail'
            ], 422);
        }

        $user = $request->user();
        $target = User::where('user_id', $request->user_id)->first();

        if (!$user || !$target) {
            return response()->json([
                'message' => 'User tidak ditemukan.',
                'status' => 'fail'
            ], 404);
        }

        if ($user->family_role === $target->family_role) {
            return response()->json([
                'message' => 'Tidak bisa pairing dengan family role yang sama.',
                'status' => 'fail'
            ], 422);
        }

        $roles = [$user->family_role, $target->family_role];
        if (!in_array('ayah', $roles) || !in_array('ibu', $roles)) {
            return response()->json([
                'message' => 'Pairing hanya boleh antara ayah dan ibu.',
                'status' => 'fail'
            ], 422);
        }


        $familyId = $target->user_id;

        $user->family_id = $familyId;
        $user->save();

        $target->family_id = $familyId;
        $target->save();

        return response()->json([
            'message' => 'Pairing berhasil.',
            'family_id' => $familyId,
            'user_1' => $user,
            'user_2' => $target,
            'status' => 'success'
        ], 200);
    }
}