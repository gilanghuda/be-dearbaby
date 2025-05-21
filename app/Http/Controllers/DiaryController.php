<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    // Read all diaries for current user
    public function index(Request $request)
    {
        $user = $request->auth_user;
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
        $user = $request->auth_user;
        $validated = $request->validate([
            'message' => 'required|string',
            'moodcheck' => 'required|in:1,2,3,4,5,6',
        ]);

        $diary = Diary::create([
            'message' => $validated['message'],
            'moodcheck' => $validated['moodcheck'],
            'user_id' => $user->user_id,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Diary created successfully.',
            'diary' => $diary,
            'status' => 'success'
        ], 201);
    }

    // Update diary milik user
    public function update(Request $request)
    {
        $user = $request->auth_user;
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['message' => 'ID is required.', 'status' => 'fail'], 400);
        }

        $diary = Diary::where('id', $id)->where('user_id', $user->user_id)->first();
        if (!$diary) {
            return response()->json(['message' => 'Diary tidak ditemukan atau bukan milik user.', 'status' => 'fail'], 404);
        }

        $validated = $request->validate([
            'message' => 'sometimes|required|string',
            'moodcheck' => 'sometimes|required|in:1,2,3,4,5,6',
        ]);

        $diary->update($validated);

        return response()->json([
            'message' => 'Diary updated successfully.',
            'diary' => $diary,
            'status' => 'success'
        ], 200);
    }

    // Delete diary milik user
    public function destroy(Request $request)
    {
        $user = $request->auth_user;
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['message' => 'ID is required.', 'status' => 'fail'], 400);
        }

        $diary = Diary::where('id', $id)->where('user_id', $user->user_id)->first();
        if (!$diary) {
            return response()->json(['message' => 'Diary tidak ditemukan atau bukan milik user.', 'status' => 'fail'], 404);
        }

        $diary->delete();

        return response()->json([
            'message' => 'Diary deleted successfully.',
            'status' => 'success'
        ], 200);
    }
}
