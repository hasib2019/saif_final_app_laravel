<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutPage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class AboutPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $aboutPage = AboutPage::where('is_active', true)->first();

            if (!$aboutPage) {
                // Return default structure if no data exists
                $aboutPage = [
                    'title' => [
                        'en' => 'About Us',
                        'ar' => 'معلومات عنا'
                    ],
                    'subtitle' => [
                        'en' => 'Discover our story, values, and our commitment to excellence.',
                        'ar' => 'اكتشف قصتنا وقيمنا والتزامنا بالتميز.'
                    ],
                    'content' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'mission' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'vision' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'values' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'history' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'team_description' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'about_image' => null,
                    'team_image' => null,
                    'office_images' => [],
                    'founded_year' => null,
                    'employees_count' => null,
                    'countries_served' => null,
                    'projects_completed' => null,
                    'achievements' => [],
                    'timeline' => []
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $aboutPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch about page content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|array',
                'title.en' => 'required|string',
                'title.ar' => 'required|string',
                'subtitle' => 'required|array',
                'subtitle.en' => 'required|string',
                'subtitle.ar' => 'required|string',
                'content' => 'required|array',
                'content.en' => 'required|string',
                'content.ar' => 'required|string',
                'mission' => 'required|array',
                'mission.en' => 'required|string',
                'mission.ar' => 'required|string',
                'vision' => 'required|array',
                'vision.en' => 'required|string',
                'vision.ar' => 'required|string',
                'about_image' => 'nullable|string',
                'team_image' => 'nullable|string',
                'founded_year' => 'nullable|integer',
                'employees_count' => 'nullable|integer',
                'countries_served' => 'nullable|integer',
                'projects_completed' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Deactivate any existing active about page
            AboutPage::where('is_active', true)->update(['is_active' => false]);

            // Create new about page
            $aboutPage = AboutPage::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'About page created successfully',
                'data' => $aboutPage
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create about page',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $aboutPage = AboutPage::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $aboutPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'About page not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|array',
                'title.en' => 'required|string',
                'title.ar' => 'required|string',
                'subtitle' => 'required|array',
                'subtitle.en' => 'required|string',
                'subtitle.ar' => 'required|string',
                'content' => 'required|array',
                'content.en' => 'required|string',
                'content.ar' => 'required|string',
                'mission' => 'required|array',
                'mission.en' => 'required|string',
                'mission.ar' => 'required|string',
                'vision' => 'required|array',
                'vision.en' => 'required|string',
                'vision.ar' => 'required|string',
                'about_image' => 'nullable|string',
                'team_image' => 'nullable|string',
                'founded_year' => 'nullable|integer',
                'employees_count' => 'nullable|integer',
                'countries_served' => 'nullable|integer',
                'projects_completed' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $aboutPage = AboutPage::findOrFail($id);
            $aboutPage->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'About page updated successfully',
                'data' => $aboutPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update about page',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $aboutPage = AboutPage::findOrFail($id);
            $aboutPage->delete();

            return response()->json([
                'success' => true,
                'message' => 'About page deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete about page',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get public about page content
     */
    public function getPublicAboutPage()
    {
        try {
            $aboutPage = AboutPage::where('is_active', true)->first();

            if (!$aboutPage) {
                return response()->json([
                    'success' => false,
                    'message' => 'About page content not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $aboutPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch about page content',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
