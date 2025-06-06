<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use Illuminate\Http\Request;

class GejalaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Gejala::all();
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data gejala.',
                'status' => 'empty',
                'data' => []
            ], 200);
        }
        return response()->json([
            'message' => 'Data gejala ditemukan.',
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->family_role !== 'admin') {
            return response()->json([
                'message' => 'Hanya admin yang boleh menambah gejala.',
                'status' => 'fail'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:ringan,sedang,serius',
        ]);

        try {
            $gejala = Gejala::create($validated);
            return response()->json([
                'message' => 'Gejala berhasil ditambahkan.',
                'gejala' => $gejala,
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat menambah gejala.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->family_role !== 'admin') {
            return response()->json([
                'message' => 'Hanya admin yang boleh mengubah gejala.',
                'status' => 'fail'
            ], 403);
        }

        $id = $request->query('id');
        if (!$id) {
            return response()->json([
                'message' => 'ID wajib diisi.',
                'status' => 'fail'
            ], 400);
        }

        $gejala = Gejala::find($id);
        if (!$gejala) {
            return response()->json([
                'message' => 'Gejala tidak ditemukan.',
                'status' => 'fail'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'sometimes|required|in:ringan,sedang,serius',
        ]);

        try {
            $gejala->update($validated);
            return response()->json([
                'message' => 'Gejala berhasil diupdate.',
                'gejala' => $gejala,
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat update gejala.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->family_role !== 'admin') {
            return response()->json([
                'message' => 'Hanya admin yang boleh menghapus gejala.',
                'status' => 'fail'
            ], 403);
        }

        $id = $request->query('id');
        if (!$id) {
            return response()->json([
                'message' => 'ID wajib diisi.',
                'status' => 'fail'
            ], 400);
        }

        $gejala = Gejala::find($id);
        if (!$gejala) {
            return response()->json([
                'message' => 'Gejala tidak ditemukan.',
                'status' => 'fail'
            ], 404);
        }

        try {
            $gejala->delete();
            return response()->json([
                'message' => 'Gejala berhasil dihapus.',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat menghapus gejala.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
