<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roleOrPermission
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $roleOrPermission, $guard = null)
    {
        $authGuard = Auth::guard($guard);
        $user = $authGuard->user();

        if (! $user) {
            throw UnauthorizedException::notLoggedIn();
        }

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        foreach ($rolesOrPermissions as $roleOrPermission) {
            if ($user->hasRole($roleOrPermission) || $user->can($roleOrPermission)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
    }
}