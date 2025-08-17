<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    /**
     * Display about content
     */
    public function index()
    {
        try {
            $about = CompanyInfo::where('is_active', true)->first();

            if (!$about) {
                // Return default structure if no data exists
                $about = [
                    'about_us' => [
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
                    'company_overview' => [
                        'en' => '',
                        'ar' => ''
                    ],
                    'achievements' => [],
                    'certifications' => [],
                    'awards' => [],
                    'about_image' => null,
                    'team_image' => null,
                    'office_images' => [],
                    'founded_year' => null,
                    'employees_count' => null,
                    'countries_served' => null,
                    'projects_completed' => null
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $about
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch about content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update about content
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'about_us' => 'required|array',
                'about_us.en' => 'required|string',
                'about_us.ar' => 'required|string',
                'mission' => 'required|array',
                'mission.en' => 'required|string',
                'mission.ar' => 'required|string',
                'vision' => 'required|array',
                'vision.en' => 'required|string',
                'vision.ar' => 'required|string',
                'values' => 'nullable|array',
                'values.en' => 'nullable|string',
                'values.ar' => 'nullable|string',
                'history' => 'nullable|array',
                'history.en' => 'nullable|string',
                'history.ar' => 'nullable|string',
                'team_description' => 'nullable|array',
                'team_description.en' => 'nullable|string',
                'team_description.ar' => 'nullable|string',
                'company_overview' => 'nullable|array',
                'company_overview.en' => 'nullable|string',
                'company_overview.ar' => 'nullable|string',
                'achievements' => 'nullable|array',
                'certifications' => 'nullable|array',
                'awards' => 'nullable|array',
                'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'team_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'office_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'employees_count' => 'nullable|integer|min:1',
                'countries_served' => 'nullable|integer|min:1',
                'projects_completed' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $about = CompanyInfo::where('is_active', true)->first();
            if (!$about) {
                $about = new CompanyInfo();
                $about->is_active = true;
            }

            // Handle about image upload
            if ($request->hasFile('about_image')) {
                // Delete old image
                if ($about->about_image) {
                    Storage::disk('public')->delete($about->about_image);
                }

                $image = $request->file('about_image');
                $imageName = 'about_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/about/' . $imageName;

                // Resize and save image
                $img = Image::read($image->getRealPath());
                $img->resize(800, 600);

                Storage::disk('public')->put($imagePath, $img->encodeByExtension($image->getClientOriginalExtension()));
                $about->about_image = $imagePath;
            }

            // Handle team image upload
            if ($request->hasFile('team_image')) {
                // Delete old image
                if ($about->team_image) {
                    Storage::disk('public')->delete($about->team_image);
                }

                $image = $request->file('team_image');
                $imageName = 'team_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/about/' . $imageName;

                // Resize and save image
                $img = Image::read($image->getRealPath());
                $img->resize(800, 600);

                Storage::disk('public')->put($imagePath, $img->encodeByExtension($image->getClientOriginalExtension()));
                $about->team_image = $imagePath;
            }

            // Handle office images upload
            if ($request->hasFile('office_images')) {
                $officeImages = [];
                foreach ($request->file('office_images') as $index => $image) {
                    $imageName = 'office_' . time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $imagePath = 'images/about/office/' . $imageName;

                    // Resize and save image
                    $img = Image::read($image->getRealPath());
                    $img->resize(800, 600);

                    Storage::disk('public')->put($imagePath, $img->encodeByExtension($image->getClientOriginalExtension()));
                    $officeImages[] = $imagePath;
                }
                $about->office_images = $officeImages;
            }

            // Update text content
            $about->about_us = $request->about_us;
            $about->mission = $request->mission;
            $about->vision = $request->vision;
            $about->values = $request->values;
            $about->history = $request->history;
            $about->team_description = $request->team_description;
            $about->company_overview = $request->company_overview;
            $about->achievements = $request->achievements ?? [];
            $about->certifications = $request->certifications ?? [];
            $about->awards = $request->awards ?? [];
            $about->founded_year = $request->founded_year;
            $about->employees_count = $request->employees_count;
            $about->countries_served = $request->countries_served;
            $about->projects_completed = $request->projects_completed;

            $about->save();

            return response()->json([
                'success' => true,
                'message' => 'About content updated successfully',
                'data' => $about
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update about content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get team members
     */
    public function team()
    {
        try {
            // This would typically come from a separate Team model
            // For now, return sample data structure
            $team = [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'position' => [
                        'en' => 'Chief Executive Officer',
                        'ar' => 'الرئيس التنفيذي'
                    ],
                    'bio' => [
                        'en' => 'Experienced leader with 15+ years in technology.',
                        'ar' => 'قائد ذو خبرة تزيد عن 15 عامًا في التكنولوجيا.'
                    ],
                    'image' => null,
                    'linkedin' => 'https://linkedin.com/in/johndoe',
                    'email' => 'john@derown.com'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $team
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch team members',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company timeline
     */
    public function timeline()
    {
        try {
            // This would typically come from a separate Timeline model
            // For now, return sample data structure
            $timeline = [
                [
                    'year' => 2020,
                    'title' => [
                        'en' => 'Company Founded',
                        'ar' => 'تأسيس الشركة'
                    ],
                    'description' => [
                        'en' => 'Derown was established with a vision to revolutionize technology solutions.',
                        'ar' => 'تم تأسيس ديرون برؤية لثورة في حلول التكنولوجيا.'
                    ],
                    'image' => null
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $timeline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch company timeline',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}