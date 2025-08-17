<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PressRelease;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PressReleaseController extends Controller
{
    /**
     * Display a listing of press releases.
     */
    public function index(Request $request)
    {
        // For admin routes, show all press releases; for public routes, only show active ones
        $isAdminRoute = strpos($request->path(), 'admin') !== false;
        $query = $isAdminRoute ? PressRelease::query() : PressRelease::where('is_active', true);

        // Search by title or content
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(title, '$.ar') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(content, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(content, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('published_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('published_at', '<=', $request->to_date);
        }

        $pressReleases = $query->orderBy('published_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $pressReleases
        ]);
    }

    /**
     * Store a newly created press release.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.ar' => 'required|string',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string|max:500',
            'description.ar' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $pressRelease = new PressRelease();
        $pressRelease->fill($request->only(['title', 'content', 'description', 'is_active', 'published_at']));

        // Set published_at to now if not provided and is_active is true
        if ($pressRelease->is_active && !$pressRelease->published_at) {
            $pressRelease->published_at = now();
        }

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = 'press-featured-' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'press-releases/featured/' . $imageName;
            
            $resizedImage = Image::read($image)->resize(800, 600);
            
            Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
            $pressRelease->featured_image = $imagePath;
        }

        // Handle additional images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = 'press-' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'press-releases/images/' . $imageName;
                
                $resizedImage = Image::read($image)->resize(800, 600);
                
                Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
                $images[] = $imagePath;
            }
            $pressRelease->images = $images;
        }

        $pressRelease->save();

        return response()->json([
            'success' => true,
            'message' => 'Press release created successfully',
            'data' => $pressRelease
        ], 201);
    }

    /**
     * Display the specified press release.
     */
    public function show(string $id)
    {
        $pressRelease = PressRelease::where('is_active', true)->find($id);

        if (!$pressRelease) {
            return response()->json([
                'success' => false,
                'message' => 'Press release not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pressRelease
        ]);
    }

    /**
     * Update the specified press release.
     */
    public function update(Request $request, string $id)
    {
        $pressRelease = PressRelease::find($id);

        if (!$pressRelease) {
            return response()->json([
                'success' => false,
                'message' => 'Press release not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.ar' => 'required|string',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string|max:500',
            'description.ar' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $pressRelease->fill($request->only(['title', 'content', 'description', 'is_active', 'published_at']));

        // Set published_at to now if not provided and is_active is true
        if ($pressRelease->is_active && !$pressRelease->published_at) {
            $pressRelease->published_at = now();
        }

        // Handle new featured image
        if ($request->hasFile('featured_image')) {
            // Delete old featured image
            if ($pressRelease->featured_image && Storage::disk('public')->exists($pressRelease->featured_image)) {
                Storage::disk('public')->delete($pressRelease->featured_image);
            }

            $image = $request->file('featured_image');
            $imageName = 'press-featured-' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'press-releases/featured/' . $imageName;
            
            $resizedImage = Image::read($image)->resize(800, 600);
            
            Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
            $pressRelease->featured_image = $imagePath;
        }

        // Handle new additional images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($pressRelease->images) {
                foreach ($pressRelease->images as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = 'press-' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'press-releases/images/' . $imageName;
                
                $resizedImage = Image::read($image)->resize(800, 600);
                
                Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
                $images[] = $imagePath;
            }
            $pressRelease->images = $images;
        }

        $pressRelease->save();

        return response()->json([
            'success' => true,
            'message' => 'Press release updated successfully',
            'data' => $pressRelease
        ]);
    }

    /**
     * Remove the specified press release.
     */
    public function destroy(string $id)
    {
        $pressRelease = PressRelease::find($id);

        if (!$pressRelease) {
            return response()->json([
                'success' => false,
                'message' => 'Press release not found'
            ], 404);
        }

        // Delete associated files
        if ($pressRelease->featured_image && Storage::disk('public')->exists($pressRelease->featured_image)) {
            Storage::disk('public')->delete($pressRelease->featured_image);
        }

        if ($pressRelease->images) {
            foreach ($pressRelease->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $pressRelease->delete();

        return response()->json([
            'success' => true,
            'message' => 'Press release deleted successfully'
        ]);
    }
}
