<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tugas = Tugas::all();

        return response()->json([
            'data'    => $tugas,
            'message' => 'Tugas retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'in:pending,selesai',
        ]);

        $tugas = Tugas::create($validated);

        return response()->json([
            'data'    => $tugas,
            'message' => 'Tugas created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tugas $tugas)
    {
        return response()->json([
            'data'    => $tugas,
            'message' => 'Tugas retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tugas $tugas)
    {
        $validated = $request->validate([
            'judul'     => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'in:pending,selesai',
        ]);

        $tugas->update($validated);

        return response()->json([
            'data'    => $tugas->fresh(),
            'message' => 'Tugas updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tugas $tugas)
    {
        $tugas->delete();

        return response()->json([
            'message' => 'Tugas deleted successfully',
        ]);
    }
}
