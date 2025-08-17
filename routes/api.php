<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AboutController;
use App\Http\Controllers\API\MediaController;
use App\Http\Controllers\API\SectorController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\PartnerController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\HealthController;
use App\Http\Controllers\API\HeroSlideController;
use App\Http\Controllers\API\MenuItemController;
use App\Http\Controllers\API\LanguageController;
use App\Http\Controllers\API\CompanyInfoController;
use App\Http\Controllers\API\PressReleaseController;
use App\Http\Controllers\API\FormSubmissionController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\TechnologyFeatureController;
use App\Models\Language;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('/health', [HealthController::class, 'check']);

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Get active languages
Route::get('/languages', function () {
    return response()->json([
        'success' => true,
        'data' => Language::getActiveLanguages()
    ]);
});

// Public content routes (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/company-info', [CompanyInfoController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/product-categories', [ProductCategoryController::class, 'index']);
    Route::get('/product-categories/{id}', [ProductCategoryController::class, 'show']);
    Route::get('/press-releases', [PressReleaseController::class, 'index']);
    Route::get('/press-releases/{id}', [PressReleaseController::class, 'show']);
    Route::get('/partners', [PartnerController::class, 'index']);
    Route::get('/hero-slides', [HeroSlideController::class, 'index']);
    Route::get('/sectors', [SectorController::class, 'index']);
    Route::get('/technology-features', [TechnologyFeatureController::class, 'index']);
    Route::post('/contact', [FormSubmissionController::class, 'store']);
    Route::get('/contact-info', [\App\Http\Controllers\ContactInfoController::class, 'getPublicContactInfo']);
});

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // User menu based on permissions
    Route::get('/menu', [MenuController::class, 'getUserMenu']);

    // Admin routes with /admin prefix
    Route::prefix('admin')->middleware('menu_permission')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('/dashboard/system-info', [DashboardController::class, 'systemInfo']);

        // About Content
        Route::middleware('permission:manage-content')->group(function () {
            Route::get('/about', [AboutController::class, 'index']);
            Route::put('/about', [AboutController::class, 'update']);
            Route::get('/about/team', [AboutController::class, 'team']);
            Route::get('/about/timeline', [AboutController::class, 'timeline']);
        });

        // Company Info
        Route::middleware('permission:manage-company-info')->group(function () {
            Route::get('/company-info', [CompanyInfoController::class, 'show']);
            Route::put('/company-info', [CompanyInfoController::class, 'update']);
        });

        // Products
        Route::middleware('permission:manage-products')->group(function () {
            Route::get('/products', [ProductController::class, 'index']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::get('/products/{id}', [ProductController::class, 'show']);
            Route::put('/products/{id}', [ProductController::class, 'update']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        });

        // Product Categories
        Route::middleware('permission:manage-products')->group(function () {
            Route::get('/product-categories', [ProductCategoryController::class, 'index']);
            Route::post('/product-categories', [ProductCategoryController::class, 'store']);
            Route::get('/product-categories/{id}', [ProductCategoryController::class, 'show']);
            Route::put('/product-categories/{id}', [ProductCategoryController::class, 'update']);
            Route::delete('/product-categories/{id}', [ProductCategoryController::class, 'destroy']);
        });

        // Press Releases (News)
        Route::middleware('permission:manage-press-releases')->group(function () {
            Route::get('/press-releases', [PressReleaseController::class, 'index']);
            Route::post('/press-releases', [PressReleaseController::class, 'store']);
            Route::get('/press-releases/{id}', [PressReleaseController::class, 'show']);
            Route::put('/press-releases/{id}', [PressReleaseController::class, 'update']);
            Route::delete('/press-releases/{id}', [PressReleaseController::class, 'destroy']);
            
            // Alias for news endpoints
            Route::get('/news', [PressReleaseController::class, 'index']);
            Route::post('/news', [PressReleaseController::class, 'store']);
            Route::get('/news/{id}', [PressReleaseController::class, 'show']);
            Route::put('/news/{id}', [PressReleaseController::class, 'update']);
            Route::delete('/news/{id}', [PressReleaseController::class, 'destroy']);
        });

        // Partners
        Route::middleware('permission:manage-partners')->group(function () {
            Route::get('/partners', [PartnerController::class, 'index']);
            Route::post('/partners', [PartnerController::class, 'store']);
            Route::get('/partners/{id}', [PartnerController::class, 'show']);
            Route::put('/partners/{id}', [PartnerController::class, 'update']);
            Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);
        });

        // Form Submissions (Contact)
        // Temporarily remove permission middleware for debugging
        // Route::middleware('permission:view-form-submissions')->group(function () {
            Route::get('/form-submissions', [FormSubmissionController::class, 'index']);
            Route::get('/form-submissions/statistics', [FormSubmissionController::class, 'statistics']);
            Route::get('/form-submissions/{id}', [FormSubmissionController::class, 'show']);
            Route::put('/form-submissions/{id}', [FormSubmissionController::class, 'update']);
            Route::delete('/form-submissions/{id}', [FormSubmissionController::class, 'destroy']);
            
            // Alias for contact endpoints
            Route::get('/contact-submissions', [FormSubmissionController::class, 'index']);
            Route::get('/contact-submissions/statistics', [FormSubmissionController::class, 'statistics']);
            Route::get('/contact-submissions/{id}', [FormSubmissionController::class, 'show']);
            Route::put('/contact-submissions/{id}', [FormSubmissionController::class, 'update']);
            Route::delete('/contact-submissions/{id}', [FormSubmissionController::class, 'destroy']);
        // });

        // Media Management
        Route::middleware('permission:manage-content')->group(function () {
            Route::get('/media', [MediaController::class, 'index']);
            Route::post('/media/upload', [MediaController::class, 'upload']);
            Route::delete('/media/{id}', [MediaController::class, 'destroy']);
        });

        // Languages
        Route::middleware('permission:manage-content')->group(function () {
            Route::get('/languages', [LanguageController::class, 'index']);
            Route::post('/languages', [LanguageController::class, 'store']);
            Route::get('/languages/{id}', [LanguageController::class, 'show']);
            Route::put('/languages/{id}', [LanguageController::class, 'update']);
            Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);
            Route::put('/languages/{id}/set-default', [LanguageController::class, 'setDefault']);
            Route::get('/languages/{code}/export', [LanguageController::class, 'export']);
            Route::post('/languages/{code}/import', [LanguageController::class, 'import']);
        });

        // Settings
        Route::middleware('permission:manage-settings')->group(function () {
            Route::get('/settings', [SettingsController::class, 'index']);
            Route::put('/settings', [SettingsController::class, 'update']);
            Route::post('/settings/upload', [SettingsController::class, 'upload']);
            Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache']);
            Route::get('/settings/backup', [SettingsController::class, 'backup']);
            Route::post('/settings/restore', [SettingsController::class, 'restore']);
            
            // Contact Information
            Route::get('/contact-info', [\App\Http\Controllers\ContactInfoController::class, 'index']);
            Route::post('/contact-info', [\App\Http\Controllers\ContactInfoController::class, 'store']);
            Route::get('/contact-info/{id}', [\App\Http\Controllers\ContactInfoController::class, 'show']);
            Route::put('/contact-info/{id}', [\App\Http\Controllers\ContactInfoController::class, 'update']);
            Route::delete('/contact-info/{id}', [\App\Http\Controllers\ContactInfoController::class, 'destroy']);
        });

        // Hero Slides
        Route::middleware('permission:manage-content')->group(function () {
              Route::get('/hero-slides', [HeroSlideController::class, 'index']);
              Route::post('/hero-slides', [HeroSlideController::class, 'store']);
              Route::get('/hero-slides/{id}', [HeroSlideController::class, 'show']);
              Route::put('/hero-slides/{id}', [HeroSlideController::class, 'update']);
              Route::patch('/hero-slides/{id}/toggle-status', [HeroSlideController::class, 'toggleStatus']);
              Route::delete('/hero-slides/{id}', [HeroSlideController::class, 'destroy']);
        });

        // User management routes (admin only)
        Route::middleware('permission:manage-users')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/roles', [UserController::class, 'roles']);
            Route::get('/users/{id}', [UserController::class, 'show']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
        });

        // Role and Permission Management
        Route::middleware('permission:manage-roles')->group(function () {
            // Roles
            Route::get('/roles', [RoleController::class, 'index']);
            Route::post('/roles', [RoleController::class, 'store']);
            Route::get('/roles/{id}', [RoleController::class, 'show']);
            Route::put('/roles/{id}', [RoleController::class, 'update']);
            Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
            
            // Permissions
            Route::get('/permissions', [PermissionController::class, 'index']);
            Route::post('/permissions', [PermissionController::class, 'store']);
            Route::get('/permissions/grouped', [PermissionController::class, 'groupedByModule']);
            Route::get('/permissions/{id}', [PermissionController::class, 'show']);
            Route::put('/permissions/{id}', [PermissionController::class, 'update']);
            Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);
        });
        
        // Module and Menu Management
        Route::middleware('permission:manage-modules')->group(function () {
            // Modules
            Route::get('/modules', [ModuleController::class, 'index']);
            Route::post('/modules', [ModuleController::class, 'store']);
            Route::get('/modules/{id}', [ModuleController::class, 'show']);
            Route::put('/modules/{id}', [ModuleController::class, 'update']);
            Route::delete('/modules/{id}', [ModuleController::class, 'destroy']);
            
            // Menu Items
            Route::get('/menu-items', [MenuItemController::class, 'index']);
            Route::post('/menu-items', [MenuItemController::class, 'store']);
            Route::get('/menu-items/{id}', [MenuItemController::class, 'show']);
            Route::put('/menu-items/{id}', [MenuItemController::class, 'update']);
            Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy']);
        });
    });

    // File upload endpoint
    Route::post('/upload', [MediaController::class, 'upload']);

    // Additional resource routes
    Route::middleware('permission:manage-content')->group(function () {
        Route::apiResource('sectors', SectorController::class);
        Route::apiResource('technology-features', TechnologyFeatureController::class);
    });
});

// Test endpoint for form submissions (no auth required)
Route::get('/test/form-submissions', [FormSubmissionController::class, 'index']);