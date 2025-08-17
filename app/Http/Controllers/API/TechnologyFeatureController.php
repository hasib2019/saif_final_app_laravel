<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TechnologyFeature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TechnologyFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TechnologyFeature::active()->ordered();
            
            // Filter by category if provided
            if ($request->has('category')) {
                $query->byCategory($request->category);
            }
            
            $features = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $features
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch technology features',
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
                'title' => 'required|array',
                'title.en' => 'required|string|max:255',
                'title.ar' => 'required|string|max:255',
                'description' => 'required|array',
                'description.en' => 'required|string',
                'description.ar' => 'required|string',
                'icon' => 'nullable|string|max:255',
                'category' => 'required|string|in:iot,ai,industry40,diagnostics',
                'benefits' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $feature = TechnologyFeature::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Technology feature created successfully',
                'data' => $feature
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create technology feature',
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
            $feature = TechnologyFeature::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $feature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Technology feature not found',
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
            $feature = TechnologyFeature::findOrFail($id);
            
            $validated = $request->validate([
                'title' => 'required|array',
                'title.en' => 'required|string|max:255',
                'title.ar' => 'required|string|max:255',
                'description' => 'required|array',
                'description.en' => 'required|string',
                'description.ar' => 'required|string',
                'icon' => 'nullable|string|max:255',
                'category' => 'required|string|in:iot,ai,industry40,diagnostics',
                'benefits' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $feature->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Technology feature updated successfully',
                'data' => $feature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update technology feature',
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
            $feature = TechnologyFeature::findOrFail($id);
            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => 'Technology feature deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete technology feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
