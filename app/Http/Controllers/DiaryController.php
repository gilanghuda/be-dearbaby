<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    // Read all diaries for current user
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'User tidak terautentikasi.',
                'status' => 'fail'
            ], 401);
        }

        $diaries = Diary::where('user_id', $user->user_id)->get();

        if ($diaries->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada diary untuk user ini.',
                'status' => 'empty',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Daftar diary ditemukan.',
            'status' => 'success',
            'data' => $diaries
        ], 200);
    }

    // Create diary, user_id diambil dari auth_user
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak terautentikasi.',
                    'status' => 'fail'
                ], 401);
            }

            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'moodcheck' => 'required|in:1,2,3,4,5,6',
            ], [
                'message.required' => 'Pesan diary wajib diisi.',
                'message.string' => 'Pesan diary harus berupa teks.',
                'message.max' => 'Pesan diary maksimal 1000 karakter.',
                'moodcheck.required' => 'Moodcheck wajib diisi.',
                'moodcheck.in' => 'Moodcheck harus antara 1 sampai 6.',
            ]);

            $diary = Diary::create([
                'message' => $validated['message'],
                'moodcheck' => (string) $validated['moodcheck'], // pastikan string
                'user_id' => $user->user_id,
                'created_at' => now(),
            ]);
            return response()->json([
                'message' => 'Diary created successfully.',
                'diary' => $diary,
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Diary create error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat membuat diary.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    // Update diary milik user
    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'User tidak terautentikasi.',
                'status' => 'fail'
            ], 401);
        }

        $id = $request->query('id');
        if (!$id) {
            return response()->json([
                'message' => 'ID diary wajib diisi.',
                'status' => 'fail'
            ], 400);
        }

        $diary = Diary::where('id', $id)->where('user_id', $user->user_id)->first();
        if (!$diary) {
            return response()->json([
                'message' => 'Diary tidak ditemukan atau bukan milik user.',
                'status' => 'fail'
            ], 404);
        }

        $validated = $request->validate([
            'message' => 'sometimes|required|string|max:1000',
            'moodcheck' => 'sometimes|required|in:1,2,3,4,5,6',
        ], [
            'message.string' => 'Pesan diary harus berupa teks.',
            'message.max' => 'Pesan diary maksimal 1000 karakter.',
            'moodcheck.in' => 'Moodcheck harus antara 1 sampai 6.',
        ]);

        try {
            $diary->update($validated);
            return response()->json([
                'message' => 'Diary updated successfully.',
                'diary' => $diary,
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat update diary.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    // Delete diary milik user
    public function destroy(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'User tidak terautentikasi.',
                'status' => 'fail'
            ], 401);
        }

        $id = $request->query('id');
        if (!$id) {
            return response()->json([
                'message' => 'ID diary wajib diisi.',
                'status' => 'fail'
            ], 400);
        }

        $diary = Diary::where('id', $id)->where('user_id', $user->user_id)->first();
        if (!$diary) {
            return response()->json([
                'message' => 'Diary tidak ditemukan atau bukan milik user.',
                'status' => 'fail'
            ], 404);
        }

        try {
            $diary->delete();
            return response()->json([
                'message' => 'Diary deleted successfully.',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat menghapus diary.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
