<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar produk berhasil diambil',
            'data'    => $produks,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $produk = Produk::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dibuat',
            'data'    => $produk,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Detail produk berhasil diambil',
            'data'    => $produk,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama'      => 'sometimes|required|string|max:255',
            'kategori'  => 'sometimes|required|string|max:100',
            'harga'     => 'sometimes|required|numeric|min:0',
            'stok'      => 'sometimes|required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $produk->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui',
            'data'    => $produk->fresh(),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus',
        ], 200);
    }
}
