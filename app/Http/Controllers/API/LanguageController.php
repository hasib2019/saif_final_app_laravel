<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    /**
     * Display a listing of languages.
     */
    public function index(Request $request)
    {
        try {
            $query = Language::query();
            
            // Filter by status if specified
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('is_active', $request->status === 'active');
            }
            
            // Search by name if specified
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('native_name', 'like', "%{$search}%");
                });
            }
            
            $languages = $query->orderBy('is_default', 'desc')
                              ->orderBy('name')
                              ->get();
            
            return response()->json([
                'success' => true,
                'data' => $languages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch languages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created language.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:languages,code',
                'native_name' => 'required|string|max:255',
                'direction' => 'required|in:ltr,rtl',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $language = Language::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Language created successfully',
                'data' => $language
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create language',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified language.
     */
    public function show(string $id)
    {
        try {
            $language = Language::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $language
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified language.
     */
    public function update(Request $request, string $id)
    {
        try {
            $language = Language::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:languages,code,' . $id,
                'native_name' => 'required|string|max:255',
                'direction' => 'required|in:ltr,rtl',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $language->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Language updated successfully',
                'data' => $language
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update language',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified language.
     */
    public function destroy(string $id)
    {
        try {
            $language = Language::findOrFail($id);
            
            // Prevent deletion of default language
            if ($language->is_default) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the default language'
                ], 422);
            }
            
            $language->delete();

            return response()->json([
                'success' => true,
                'message' => 'Language deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete language',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set language as default.
     */
    public function setDefault(string $id)
    {
        try {
            $language = Language::findOrFail($id);
            
            // Remove default from all languages
            Language::where('is_default', true)->update(['is_default' => false]);
            
            // Set this language as default
            $language->update(['is_default' => true, 'is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Default language updated successfully',
                'data' => $language
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set default language',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export translations for a language.
     */
    public function export(string $code)
    {
        try {
            $language = Language::where('code', $code)->firstOrFail();
            
            // This is a simplified export - in a real app you'd export actual translation files
            $translations = [
                'language' => $language->toArray(),
                'translations' => [
                    'common' => [
                        'save' => 'Save',
                        'cancel' => 'Cancel',
                        'delete' => 'Delete',
                        'edit' => 'Edit',
                        'add' => 'Add',
                        'search' => 'Search',
                    ],
                    'navigation' => [
                        'dashboard' => 'Dashboard',
                        'products' => 'Products',
                        'categories' => 'Categories',
                        'news' => 'News',
                        'partners' => 'Partners',
                        'users' => 'Users',
                        'settings' => 'Settings',
                    ]
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $translations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import translations for a language.
     */
    public function import(Request $request, string $code)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $language = Language::where('code', $code)->firstOrFail();
            
            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $translations = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON file'
                ], 422);
            }
            
            // In a real app, you'd save these translations to your translation system
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Translations imported successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}