<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $slides = HeroSlide::active()->ordered()->get();
            
            return response()->json([
                'success' => true,
                'data' => $slides
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch hero slides',
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
            // Log the incoming request data for debugging
            \Log::info('HeroSlide create request data:', $request->all());
            
            $validated = $request->validate([
                'title_en' => 'required|string|max:255',
                'title_ar' => 'nullable|string|max:255',
                'title_bn' => 'nullable|string|max:255',
                'subtitle_en' => 'nullable|string|max:255',
                'subtitle_ar' => 'nullable|string|max:255',
                'subtitle_bn' => 'nullable|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'description_bn' => 'nullable|string',
                'button_text_en' => 'nullable|string|max:100',
                'button_text_ar' => 'nullable|string|max:100',
                'button_text_bn' => 'nullable|string|max:100',
                'button_url' => 'nullable|string|max:255',
                'image' => 'required|string|max:255',
                'background_image' => 'nullable|string|max:255',
                'video_url' => 'nullable|string|max:255',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'show_overlay' => 'boolean',
                'overlay_opacity' => 'nullable|integer|min:0|max:100',
                'text_position' => 'nullable|string|in:left,center,right',
                'animation_type' => 'nullable|string|in:fade,slide,zoom'
            ]);

            // Transform the data for JSON fields
            $data = [
                'title' => [
                    'en' => $validated['title_en'],
                    'ar' => $validated['title_ar'] ?? null,
                    'bn' => $validated['title_bn'] ?? null,
                ],
                'subtitle' => [
                    'en' => $validated['subtitle_en'] ?? null,
                    'ar' => $validated['subtitle_ar'] ?? null,
                    'bn' => $validated['subtitle_bn'] ?? null,
                ],
                'description' => [
                    'en' => $validated['description_en'] ?? null,
                    'ar' => $validated['description_ar'] ?? null,
                    'bn' => $validated['description_bn'] ?? null,
                ],
                'button_text' => [
                    'en' => $validated['button_text_en'] ?? null,
                    'ar' => $validated['button_text_ar'] ?? null,
                    'bn' => $validated['button_text_bn'] ?? null,
                ],
                'button_link' => $validated['button_url'] ?? null,
                'image' => $validated['image'],
                'background_image' => $validated['background_image'] ?? null,
                'video_url' => $validated['video_url'] ?? null,
                'sort_order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ];

            $slide = HeroSlide::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Hero slide created successfully',
                'data' => $slide
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating hero slide: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create hero slide',
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
            $slide = HeroSlide::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $slide
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hero slide not found',
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
            $slide = HeroSlide::findOrFail($id);
            
            // Log the incoming request data for debugging
            \Log::info('HeroSlide update request data:', $request->all());
            
            $validated = $request->validate([
                'title_en' => 'required|string|max:255',
                'title_ar' => 'nullable|string|max:255',
                'title_bn' => 'nullable|string|max:255',
                'subtitle_en' => 'nullable|string|max:255',
                'subtitle_ar' => 'nullable|string|max:255',
                'subtitle_bn' => 'nullable|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'description_bn' => 'nullable|string',
                'button_text_en' => 'nullable|string|max:100',
                'button_text_ar' => 'nullable|string|max:100',
                'button_text_bn' => 'nullable|string|max:100',
                'button_url' => 'nullable|string|max:255',
                'image' => 'required|string|max:255',
                'background_image' => 'nullable|string|max:255',
                'video_url' => 'nullable|string|max:255',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'show_overlay' => 'boolean',
                'overlay_opacity' => 'nullable|integer|min:0|max:100',
                'text_position' => 'nullable|string|in:left,center,right',
                'animation_type' => 'nullable|string|in:fade,slide,zoom'
            ]);

            // Transform the data for JSON fields (same as store method)
            $data = [
                'title' => [
                    'en' => $validated['title_en'],
                    'ar' => $validated['title_ar'] ?? null,
                    'bn' => $validated['title_bn'] ?? null,
                ],
                'subtitle' => [
                    'en' => $validated['subtitle_en'] ?? null,
                    'ar' => $validated['subtitle_ar'] ?? null,
                    'bn' => $validated['subtitle_bn'] ?? null,
                ],
                'description' => [
                    'en' => $validated['description_en'] ?? null,
                    'ar' => $validated['description_ar'] ?? null,
                    'bn' => $validated['description_bn'] ?? null,
                ],
                'button_text' => [
                    'en' => $validated['button_text_en'] ?? null,
                    'ar' => $validated['button_text_ar'] ?? null,
                    'bn' => $validated['button_text_bn'] ?? null,
                ],
                'button_link' => $validated['button_url'] ?? null,
                'image' => $validated['image'],
                'background_image' => $validated['background_image'] ?? null,
                'video_url' => $validated['video_url'] ?? null,
                'sort_order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
                'show_overlay' => $validated['show_overlay'] ?? true,
                'overlay_opacity' => $validated['overlay_opacity'] ?? 50,
                'text_position' => $validated['text_position'] ?? 'center',
                'animation_type' => $validated['animation_type'] ?? 'fade'
            ];

            $slide->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Hero slide updated successfully',
                'data' => $slide
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating hero slide: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update hero slide',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $slide = HeroSlide::findOrFail($id);
            $slide->is_active = !$slide->is_active;
            $slide->save();

            return response()->json([
                'success' => true,
                'message' => 'Hero slide status updated successfully',
                'data' => $slide
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update hero slide status',
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
            $slide = HeroSlide::findOrFail($id);
            $slide->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hero slide deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete hero slide',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
