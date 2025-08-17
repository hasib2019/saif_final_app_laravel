<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Get the menu structure for the authenticated user based on their permissions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserMenu(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }
        
        // Get all active modules ordered by their order field
        $modules = Module::where('is_active', true)
            ->orderBy('order')
            ->get();
            
        $userMenu = [];
        
        foreach ($modules as $module) {
            // Get top-level menu items for this module
            $menuItems = MenuItem::where('module_id', $module->id)
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();
                
            $moduleMenuItems = [];
            
            foreach ($menuItems as $menuItem) {
                // Check if user has any of the permissions required for this menu item
                $hasPermission = false;
                $permissions = $menuItem->permissions->pluck('name')->toArray();
                
                if (empty($permissions)) {
                    // If no permissions are required, show the menu item
                    $hasPermission = true;
                } else {
                    foreach ($permissions as $permission) {
                        if ($user->hasPermissionTo($permission)) {
                            $hasPermission = true;
                            break;
                        }
                    }
                }
                
                if ($hasPermission) {
                    // Get children menu items
                    $children = $this->getChildMenuItems($menuItem->id, $user);
                    
                    // Only add menu item if it has children or is a leaf node
                    if (!empty($children) || $menuItem->url) {
                        $moduleMenuItems[] = [
                            'id' => $menuItem->id,
                            'name' => $menuItem->name,
                            'url' => $menuItem->url,
                            'icon' => $menuItem->icon,
                            'children' => $children
                        ];
                    }
                }
            }
            
            // Only add module if it has menu items
            if (!empty($moduleMenuItems)) {
                $userMenu[] = [
                    'id' => $module->id,
                    'name' => $module->name,
                    'description' => $module->description,
                    'icon' => $module->icon,
                    'menuItems' => $moduleMenuItems
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $userMenu
        ]);
    }
    
    /**
     * Recursively get child menu items for a parent menu item
     *
     * @param  int  $parentId
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getChildMenuItems($parentId, $user)
    {
        $children = [];
        
        $menuItems = MenuItem::where('parent_id', $parentId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
            
        foreach ($menuItems as $menuItem) {
            // Check if user has any of the permissions required for this menu item
            $hasPermission = false;
            $permissions = $menuItem->permissions->pluck('name')->toArray();
            
            if (empty($permissions)) {
                // If no permissions are required, show the menu item
                $hasPermission = true;
            } else {
                foreach ($permissions as $permission) {
                    if ($user->hasPermissionTo($permission)) {
                        $hasPermission = true;
                        break;
                    }
                }
            }
            
            if ($hasPermission) {
                // Get grandchildren menu items
                $grandchildren = $this->getChildMenuItems($menuItem->id, $user);
                
                // Only add menu item if it has children or is a leaf node
                if (!empty($grandchildren) || $menuItem->url) {
                    $children[] = [
                        'id' => $menuItem->id,
                        'name' => $menuItem->name,
                        'url' => $menuItem->url,
                        'icon' => $menuItem->icon,
                        'children' => $grandchildren
                    ];
                }
            }
        }
        
        return $children;
    }
}