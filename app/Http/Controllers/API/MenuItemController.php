<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the menu items.
     */
    public function index(Request $request)
    {
        $query = MenuItem::with(['module', 'parent', 'children', 'permissions']);

        // Filter by module
        if ($request->has('module_id')) {
            $query->where('module_id', $request->module_id);
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        } else if ($request->has('top_level') && $request->top_level) {
            $query->whereNull('parent_id');
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $menuItems = $query->orderBy('order', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $menuItems
        ]);
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:menu_items,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if module exists
        $module = Module::find($request->module_id);
        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found'
            ], 404);
        }

        // Check if parent exists if provided
        if ($request->has('parent_id') && $request->parent_id) {
            $parent = MenuItem::find($request->parent_id);
            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent menu item not found'
                ], 404);
            }
        }

        $menuItem = MenuItem::create([
            'module_id' => $request->module_id,
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
            'parent_id' => $request->parent_id,
        ]);

        // Attach permissions if provided
        if ($request->has('permission_ids') && is_array($request->permission_ids)) {
            $menuItem->permissions()->attach($request->permission_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully',
            'data' => $menuItem->load(['module', 'parent', 'permissions'])
        ], 201);
    }

    /**
     * Display the specified menu item.
     */
    public function show(string $id)
    {
        $menuItem = MenuItem::with(['module', 'parent', 'children', 'permissions'])->find($id);

        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $menuItem
        ]);
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, string $id)
    {
        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:menu_items,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Prevent circular reference
        if ($request->parent_id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'A menu item cannot be its own parent'
            ], 422);
        }

        $menuItem->update([
            'module_id' => $request->module_id,
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'order' => $request->order ?? $menuItem->order,
            'is_active' => $request->is_active ?? $menuItem->is_active,
            'parent_id' => $request->parent_id,
        ]);

        // Sync permissions if provided
        if ($request->has('permission_ids')) {
            $menuItem->permissions()->sync($request->permission_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => $menuItem->load(['module', 'parent', 'permissions'])
        ]);
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy(string $id)
    {
        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }

        $menuItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully'
        ]);
    }
}