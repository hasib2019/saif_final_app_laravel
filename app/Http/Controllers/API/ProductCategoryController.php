<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductCategory;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::where('is_active', true);

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        $categories = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created product category.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = new ProductCategory();
        $category->fill($request->only(['name', 'description', 'is_active', 'sort_order']));
        
        // Generate slug from English name
        $englishName = $request->input('name.en');
        $slug = \Illuminate\Support\Str::slug($englishName);
        
        // Check if slug already exists and make it unique if needed
        $originalSlug = $slug;
        $count = 1;
        
        while (ProductCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        
        $category->slug = $slug;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'category-' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'categories/' . $imageName;
            
            $resizedImage = Image::read($image)->resize(400, 300);
            
            Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
            $category->image = $imagePath;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Product category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified product category.
     */
    public function show(string $id)
    {
        $category = ProductCategory::where('is_active', true)->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Product category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified product category.
     */
    public function update(Request $request, string $id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Product category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if English name has changed
        $oldEnglishName = $category->getTranslation('name', 'en');
        $newEnglishName = $request->input('name.en');
        
        $category->fill($request->only(['name', 'description', 'is_active', 'sort_order']));
        
        // Update slug if name has changed
        if ($oldEnglishName !== $newEnglishName) {
            $slug = \Illuminate\Support\Str::slug($newEnglishName);
            
            // Check if slug already exists and make it unique if needed
            $originalSlug = $slug;
            $count = 1;
            
            while (ProductCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            
            $category->slug = $slug;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $image = $request->file('image');
            $imageName = 'category-' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'categories/' . $imageName;
            
            $resizedImage = Image::read($image)->resize(400, 300);
            
            Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
            $category->image = $imagePath;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Product category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified product category.
     */
    public function destroy(string $id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Product category not found'
            ], 404);
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing products'
            ], 422);
        }

        // Delete associated image
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product category deleted successfully'
        ]);
    }
}