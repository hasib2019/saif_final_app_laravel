<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;

class CheckMenuItemPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Get the current URL path
        $currentPath = $request->path();
        
        // Convert API path to frontend path format for comparison
        // Example: 'api/admin/users' to '/admin/users'
        $frontendPath = '/' . str_replace('api/', '', $currentPath);
        
        // Find menu items that match this path
        $menuItem = MenuItem::where('url', 'like', '%' . $frontendPath . '%')->first();
        
        if (!$menuItem) {
            // If no menu item found for this path, check if user has any admin role
            // This allows access to routes that might not be explicitly in the menu
            if ($user->hasRole('admin')) {
                return $next($request);
            }
            
            return response()->json(['message' => 'Menu item not found for this path.'], 404);
        }
        
        // Get permissions associated with this menu item
        $permissions = $menuItem->permissions->pluck('name')->toArray();
        
        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($user->hasPermissionTo($permission)) {
                return $next($request);
            }
        }
        
        return response()->json(['message' => 'Unauthorized. You do not have the necessary permissions.'], 403);
    }
}