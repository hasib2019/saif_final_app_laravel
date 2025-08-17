<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Partner;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * Display a listing of partners.
     */
    public function index(Request $request)
    {
        $query = Partner::where('is_active', true);

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $partners = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $partners
        ]);
    }

    /**
     * Store a newly created partner.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'website_url' => 'nullable|url|max:255',
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

        $partner = new Partner();
        $partner->fill($request->only(['name', 'description', 'website_url', 'is_active', 'sort_order']));

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'partner-logo-' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = 'partners/logos/' . $logoName;
            
            // For SVG files, store directly without resizing
            if ($logo->getClientOriginalExtension() === 'svg') {
                $logo->storeAs('public/partners/logos', $logoName);
            } else {
                $resizedLogo = Image::read($logo)->resize(300, 200);
                
                Storage::disk('public')->put($logoPath, $resizedLogo->encodeByExtension($logo->getClientOriginalExtension()));
            }
            
            $partner->logo = $logoPath;
        }

        $partner->save();

        return response()->json([
            'success' => true,
            'message' => 'Partner created successfully',
            'data' => $partner
        ], 201);
    }

    /**
     * Display the specified partner.
     */
    public function show(string $id)
    {
        $partner = Partner::where('is_active', true)->find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $partner
        ]);
    }

    /**
     * Update the specified partner.
     */
    public function update(Request $request, string $id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'website_url' => 'nullable|url|max:255',
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

        $partner->fill($request->only(['name', 'description', 'website_url', 'is_active', 'sort_order']));

        // Handle new logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($partner->logo && Storage::disk('public')->exists($partner->logo)) {
                Storage::disk('public')->delete($partner->logo);
            }

            $logo = $request->file('logo');
            $logoName = 'partner-logo-' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = 'partners/logos/' . $logoName;
            
            // For SVG files, store directly without resizing
            if ($logo->getClientOriginalExtension() === 'svg') {
                $logo->storeAs('public/partners/logos', $logoName);
            } else {
                $resizedLogo = Image::read($logo)->resize(300, 200);
                
                Storage::disk('public')->put($logoPath, $resizedLogo->encodeByExtension($logo->getClientOriginalExtension()));
            }
            
            $partner->logo = $logoPath;
        }

        $partner->save();

        return response()->json([
            'success' => true,
            'message' => 'Partner updated successfully',
            'data' => $partner
        ]);
    }

    /**
     * Remove the specified partner.
     */
    public function destroy(string $id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found'
            ], 404);
        }

        // Delete associated logo
        if ($partner->logo && Storage::disk('public')->exists($partner->logo)) {
            Storage::disk('public')->delete($partner->logo);
        }

        $partner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Partner deleted successfully'
        ]);
    }
}
