<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $sectors = Sector::active()->ordered()->get();
            
            return response()->json([
                'success' => true,
                'data' => $sectors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sectors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|array',
                'name.en' => 'required|string|max:255',
                'name.ar' => 'required|string|max:255',
                'description' => 'required|array',
                'description.en' => 'required|string',
                'description.ar' => 'required|string',
                'icon' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:255',
                'use_cases' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $sector = Sector::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sector created successfully',
                'data' => $sector
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $sector = Sector::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $sector
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sector not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $sector = Sector::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|array',
                'name.en' => 'required|string|max:255',
                'name.ar' => 'required|string|max:255',
                'description' => 'required|array',
                'description.en' => 'required|string',
                'description.ar' => 'required|string',
                'icon' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:255',
                'use_cases' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $sector->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sector updated successfully',
                'data' => $sector
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $sector = Sector::findOrFail($id);
            $sector->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sector deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sector',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
