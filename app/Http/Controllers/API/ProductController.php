<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductCategory;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        $products = $query->orderBy('sort_order')->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Store a newly created product.
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
            'specifications' => 'nullable|array',
            'specifications.en' => 'nullable|string',
            'specifications.ar' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|string',
            'videos.*' => 'nullable|mimes:mp4,avi,mov|max:10240',
            'catalog_file' => 'nullable|mimes:pdf|max:5120',
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

        $product = new Product();
        $product->fill($request->only(['name', 'description', 'specifications', 'category_id', 'is_active', 'sort_order']));

        // Handle images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = 'product-' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'products/images/' . $imageName;
                
                // Create the directory if it doesn't exist
                $directory = storage_path('app/public/products/images');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                $resizedImage = Image::read($image)->resize(800, 600);
                
                Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
                
                // For debugging
                \Log::info('Image saved to: ' . $imagePath);
                $images[] = $imagePath;
            }
            $product->images = $images;
        }

        // Handle videos
        if ($request->hasFile('videos')) {
            $videos = [];
            foreach ($request->file('videos') as $video) {
                $videoName = 'product-video-' . time() . '-' . uniqid() . '.' . $video->getClientOriginalExtension();
                $videoPath = 'products/videos/' . $videoName;
                $video->storeAs('public/products/videos', $videoName);
                $videos[] = $videoPath;
            }
            $product->videos = $videos;
        }

        // Handle catalog file
        if ($request->hasFile('catalog_file')) {
            $catalog = $request->file('catalog_file');
            $catalogName = 'catalog-' . time() . '.' . $catalog->getClientOriginalExtension();
            $catalogPath = 'products/catalogs/' . $catalogName;
            $catalog->storeAs('public/products/catalogs', $catalogName);
            $product->catalog_file = $catalogPath;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('category')
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->where('is_active', true)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'specifications' => 'nullable|array',
            'specifications.en' => 'nullable|string',
            'specifications.ar' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|string',
            'videos.*' => 'nullable|mimes:mp4,avi,mov|max:10240',
            'catalog_file' => 'nullable|mimes:pdf|max:5120',
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

        $product->fill($request->only(['name', 'description', 'specifications', 'category_id', 'is_active', 'sort_order']));

        // Handle images
        if ($request->hasFile('images')) {
            // Keep track of existing images if they were sent
            $existingImages = $request->input('existing_images', []);
            
            // If we have new images but no existing images were specified, delete all old images
            if (empty($existingImages) && $product->images) {
                foreach ($product->images as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
                $images = [];
            } else {
                // Keep the existing images that were specified
                $images = is_array($existingImages) ? $existingImages : [];
            }
            
            // Add new images
            foreach ($request->file('images') as $image) {
                $imageName = 'product-' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'products/images/' . $imageName;
                
                // Create the directory if it doesn't exist
                $directory = storage_path('app/public/products/images');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                $resizedImage = Image::read($image)->resize(800, 600);
                
                Storage::disk('public')->put($imagePath, $resizedImage->encodeByExtension($image->getClientOriginalExtension()));
                
                // For debugging
                \Log::info('Image saved to: ' . $imagePath);
                $images[] = $imagePath;
            }
            $product->images = $images;
        } elseif ($request->has('existing_images')) {
             // If only existing_images were sent (no new uploads)
             $existingImages = $request->input('existing_images');
             
             // If existing_images is empty array, it means all images should be removed
             if (empty($existingImages)) {
                 if ($product->images) {
                     foreach ($product->images as $oldImage) {
                         if (Storage::disk('public')->exists($oldImage)) {
                             Storage::disk('public')->delete($oldImage);
                         }
                     }
                 }
                 $product->images = [];
             } else {
                 // Remove images that are not in the existing_images array
                 if ($product->images) {
                     foreach ($product->images as $oldImage) {
                         if (!in_array($oldImage, $existingImages) && Storage::disk('public')->exists($oldImage)) {
                             Storage::disk('public')->delete($oldImage);
                         }
                     }
                 }
                 $product->images = $existingImages;
             }
         }
         
        // Handle videos
        if ($request->hasFile('videos')) {
            $existingVideos = $request->input('existing_videos', []);
            
            // If we have new videos but no existing videos were specified, delete all old videos
            if (empty($existingVideos) && $product->videos) {
                foreach ($product->videos as $oldVideo) {
                    if (Storage::disk('public')->exists($oldVideo)) {
                        Storage::disk('public')->delete($oldVideo);
                    }
                }
                $videos = [];
            } else {
                // Keep the existing videos that were specified
                $videos = is_array($existingVideos) ? $existingVideos : [];
            }
            
            // Add new videos
            foreach ($request->file('videos') as $video) {
                $videoName = 'product-video-' . time() . '-' . uniqid() . '.' . $video->getClientOriginalExtension();
                $videoPath = 'products/videos/' . $videoName;
                $video->storeAs('public/products/videos', $videoName);
                $videos[] = $videoPath;
            }
            $product->videos = $videos;
        } elseif ($request->has('existing_videos')) {
             // If only existing_videos were sent (no new uploads)
             $existingVideos = $request->input('existing_videos');
             
             // If existing_videos is empty array, it means all videos should be removed
             if (empty($existingVideos)) {
                 if ($product->videos) {
                     foreach ($product->videos as $oldVideo) {
                         if (Storage::disk('public')->exists($oldVideo)) {
                             Storage::disk('public')->delete($oldVideo);
                         }
                     }
                 }
                 $product->videos = [];
             } else {
                 // Remove videos that are not in the existing_videos array
                 if ($product->videos) {
                     foreach ($product->videos as $oldVideo) {
                         if (!in_array($oldVideo, $existingVideos) && Storage::disk('public')->exists($oldVideo)) {
                             Storage::disk('public')->delete($oldVideo);
                         }
                     }
                 }
                 $product->videos = $existingVideos;
             }
         }
         
        // Handle catalog file
        if ($request->hasFile('catalog_file')) {
            // Delete old catalog file if it exists
            if ($product->catalog_file && Storage::disk('public')->exists($product->catalog_file)) {
                Storage::disk('public')->delete($product->catalog_file);
            }
            
            $catalog = $request->file('catalog_file');
            $catalogName = 'catalog-' . time() . '.' . $catalog->getClientOriginalExtension();
            $catalogPath = 'products/catalogs/' . $catalogName;
            $catalog->storeAs('public/products/catalogs', $catalogName);
            $product->catalog_file = $catalogPath;
        } elseif ($request->has('remove_catalog') && $request->input('remove_catalog')) {
            // Remove catalog file if requested
            if ($product->catalog_file && Storage::disk('public')->exists($product->catalog_file)) {
                Storage::disk('public')->delete($product->catalog_file);
            }
            $product->catalog_file = null;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('category')
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Delete associated files
        if ($product->images) {
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        if ($product->videos) {
            foreach ($product->videos as $video) {
                if (Storage::disk('public')->exists($video)) {
                    Storage::disk('public')->delete($video);
                }
            }
        }

        if ($product->catalog_file && Storage::disk('public')->exists($product->catalog_file)) {
            Storage::disk('public')->delete($product->catalog_file);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
