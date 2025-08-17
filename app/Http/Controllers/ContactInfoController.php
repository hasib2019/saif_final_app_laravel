<?php

namespace App\Http\Controllers;

use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ContactInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactInfo = ContactInfo::where('is_active', true)->first();
        
        if (!$contactInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Contact information retrieved successfully',
            'data' => $contactInfo
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'business_hours' => 'nullable|string',
            'map_latitude' => 'nullable|string|max:255',
            'map_longitude' => 'nullable|string|max:255',
            'map_embed_code' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Check if contact info already exists
        $existingInfo = ContactInfo::first();
        
        if ($existingInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information already exists. Use update instead.',
                'data' => $existingInfo
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $contactInfo = ContactInfo::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Contact information created successfully',
            'data' => $contactInfo
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contactInfo = ContactInfo::find($id);
        
        if (!$contactInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Contact information retrieved successfully',
            'data' => $contactInfo
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'business_hours' => 'nullable|string',
            'map_latitude' => 'nullable|string|max:255',
            'map_longitude' => 'nullable|string|max:255',
            'map_embed_code' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $contactInfo = ContactInfo::find($id);
        
        if (!$contactInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }
        
        $contactInfo->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Contact information updated successfully',
            'data' => $contactInfo
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contactInfo = ContactInfo::find($id);
        
        if (!$contactInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }
        
        $contactInfo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Contact information deleted successfully',
            'data' => null
        ], Response::HTTP_OK);
    }
    
    /**
     * Get public contact information
     */
    public function getPublicContactInfo()
    {
        $contactInfo = ContactInfo::where('is_active', true)->first();
        
        if (!$contactInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Contact information retrieved successfully',
            'data' => $contactInfo
        ], Response::HTTP_OK);
    }
}
