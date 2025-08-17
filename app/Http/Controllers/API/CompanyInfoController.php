<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CompanyInfo;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CompanyInfoController extends Controller
{
    /**
     * Display the company information.
     */
    public function show(Request $request)
    {
        $companyInfo = CompanyInfo::where('is_active', true)->first();

        if (!$companyInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Company information not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $companyInfo
        ]);
    }

    /**
     * Update the company information.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mission' => 'required|array',
            'mission.en' => 'required|string',
            'mission.ar' => 'required|string',
            'history' => 'required|array',
            'history.en' => 'required|string',
            'history.ar' => 'required|string',
            'values' => 'required|array',
            'values.en' => 'required|string',
            'values.ar' => 'required|string',
            'initiatives' => 'required|array',
            'initiatives.en' => 'required|string',
            'initiatives.ar' => 'required|string',
            'company_overview' => 'nullable|array',
            'company_overview.en' => 'nullable|string',
            'company_overview.ar' => 'nullable|string',
            'team_description' => 'nullable|array',
            'team_description.en' => 'nullable|string',
            'team_description.ar' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $companyInfo = CompanyInfo::firstOrNew();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($companyInfo->logo && Storage::disk('public')->exists($companyInfo->logo)) {
                Storage::disk('public')->delete($companyInfo->logo);
            }

            $logo = $request->file('logo');
            $logoName = 'company-logo-' . time() . '.' . $logo->getClientOriginalExtension();
            
            // Resize and save logo
            $image = Image::read($logo)->resize(300, 300);
            
            $logoPath = 'logos/' . $logoName;
            Storage::disk('public')->put($logoPath, $image->encodeByExtension($logo->getClientOriginalExtension()));
            
            $companyInfo->logo = $logoPath;
        }

        $companyInfo->fill([
            'mission' => $request->mission,
            'history' => $request->history,
            'values' => $request->values,
            'initiatives' => $request->initiatives,
            'company_overview' => $request->company_overview,
            'team_description' => $request->team_description,
            'is_active' => $request->get('is_active', true),
        ]);

        $companyInfo->save();

        return response()->json([
            'success' => true,
            'message' => 'Company information updated successfully',
            'data' => $companyInfo
        ]);
    }
}
