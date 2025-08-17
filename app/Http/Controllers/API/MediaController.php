<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class MediaController extends Controller
{
    /**
     * Display a listing of media files.
     */
    public function index(Request $request)
    {
        try {
            $mediaPath = public_path('storage');
            $files = [];
            
            if (is_dir($mediaPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($mediaPath)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $relativePath = str_replace($mediaPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                        $relativePath = str_replace('\\', '/', $relativePath);
                        
                        $fileInfo = [
                            'id' => md5($relativePath),
                            'name' => $file->getFilename(),
                            'path' => $relativePath,
                            'url' => asset('storage/' . $relativePath),
                            'size' => $file->getSize(),
                            'type' => $this->getFileType($file->getExtension()),
                            'extension' => $file->getExtension(),
                            'created_at' => date('Y-m-d H:i:s', $file->getCTime()),
                            'updated_at' => date('Y-m-d H:i:s', $file->getMTime()),
                        ];
                        
                        $files[] = $fileInfo;
                    }
                }
            }
            
            // Filter by type if specified
            if ($request->has('type') && $request->type !== 'all') {
                $files = array_filter($files, function($file) use ($request) {
                    return $file['type'] === $request->type;
                });
            }
            
            // Search by name if specified
            if ($request->has('search')) {
                $search = strtolower($request->search);
                $files = array_filter($files, function($file) use ($search) {
                    return strpos(strtolower($file['name']), $search) !== false;
                });
            }
            
            // Sort by created_at desc
            usort($files, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return response()->json([
                'success' => true,
                'data' => array_values($files)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch media files',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload media files.
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:3072', // 3MB max for hero slider images
                'files.*' => 'nullable|file|max:3072', // 3MB max for multiple files
                'folder' => 'nullable|string|max:255',
                'type' => 'nullable|string|in:image,video,audio,document,other',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $uploadedFiles = [];
            $folder = $request->folder ?? 'uploads';
            
            // Handle single file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $filePath = $folder . '/' . $fileName;

                // Handle image resizing for images
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $image = Image::read($file);
                    
                    // Resize if too large
                    if ($image->width() > 1920 || $image->height() > 1080) {
                        $image->resize(1920, 1080, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    
                    // Ensure images with dimensions around 1351 × 600 are properly handled
                    if ($image->width() >= 1351 && $image->height() >= 600) {
                        $image->resize(1350, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    
                    Storage::disk('public')->put($filePath, $image->encodeByExtension($extension));
                } else {
                    Storage::disk('public')->putFileAs($folder, $file, $fileName);
                }

                $uploadedFile = [
                    'id' => md5($filePath),
                    'name' => $originalName,
                    'path' => $filePath,
                    'url' => asset('storage/' . $filePath),
                    'size' => $file->getSize(),
                    'type' => $this->getFileType($extension),
                    'extension' => $extension,
                ];
                
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'data' => $uploadedFile,
                    'url' => asset('storage/' . $filePath)
                ], 201);
            }
            
            // Handle multiple files upload
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '_' . uniqid() . '.' . $extension;
                    $filePath = $folder . '/' . $fileName;

                    // Handle image resizing for images
                    if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $image = Image::read($file);
                        
                        // Resize if too large
                        if ($image->width() > 1920 || $image->height() > 1080) {
                            $image->resize(1920, 1080, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                        
                        // Ensure images with dimensions around 1351 × 600 are properly handled
                        if ($image->width() >= 1351 && $image->height() >= 600) {
                            $image->resize(1350, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                        
                        Storage::disk('public')->put($filePath, $image->encodeByExtension($extension));
                    } else {
                        Storage::disk('public')->putFileAs($folder, $file, $fileName);
                    }

                    $uploadedFiles[] = [
                        'id' => md5($filePath),
                        'name' => $originalName,
                        'path' => $filePath,
                        'url' => asset('storage/' . $filePath),
                        'size' => $file->getSize(),
                        'type' => $this->getFileType($extension),
                        'extension' => $extension,
                    ];
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Files uploaded successfully',
                    'data' => $uploadedFiles
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload files',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a media file.
     */
    public function destroy(string $id)
    {
        try {
            // Find file by ID (which is md5 of path)
            $mediaPath = public_path('storage');
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($mediaPath)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace($mediaPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $relativePath = str_replace('\\', '/', $relativePath);
                    
                    if (md5($relativePath) === $id) {
                        Storage::disk('public')->delete($relativePath);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'File deleted successfully'
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file type based on extension.
     */
    private function getFileType($extension)
    {
        $extension = strtolower($extension);
        
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
        $videoTypes = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        $audioTypes = ['mp3', 'wav', 'ogg', 'aac', 'flac'];
        $documentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        
        if (in_array($extension, $imageTypes)) {
            return 'image';
        } elseif (in_array($extension, $videoTypes)) {
            return 'video';
        } elseif (in_array($extension, $audioTypes)) {
            return 'audio';
        } elseif (in_array($extension, $documentTypes)) {
            return 'document';
        }
        
        return 'other';
    }
}