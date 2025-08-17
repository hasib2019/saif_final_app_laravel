<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Module;
use App\Models\MenuItem;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create modules
        $dashboardModule = Module::firstOrCreate(
            ['name' => 'Dashboard'],
            [
                'description' => 'Dashboard and analytics',
                'icon' => 'dashboard',
                'order' => 1,
                'is_active' => true,
            ]
        );

        $contentModule = Module::firstOrCreate(
            ['name' => 'Content Management'],
            [
                'description' => 'Manage website content',
                'icon' => 'content_paste',
                'order' => 2,
                'is_active' => true,
            ]
        );

        $productModule = Module::firstOrCreate(
            ['name' => 'Products'],
            [
                'description' => 'Manage products and categories',
                'icon' => 'inventory_2',
                'order' => 3,
                'is_active' => true,
            ]
        );

        $userModule = Module::firstOrCreate(
            ['name' => 'User Management'],
            [
                'description' => 'Manage users and roles',
                'icon' => 'people',
                'order' => 4,
                'is_active' => true,
            ]
        );

        $settingsModule = Module::firstOrCreate(
            ['name' => 'Settings'],
            [
                'description' => 'System settings and configuration',
                'icon' => 'settings',
                'order' => 5,
                'is_active' => true,
            ]
        );

        // Create permissions
        $permissions = [
            // Dashboard permissions
            'view-dashboard' => 'View dashboard and analytics',
            
            // Content permissions
            'manage-content' => 'Manage website content',
            'manage-company-info' => 'Manage company information',
            'manage-press-releases' => 'Manage press releases and news',
            'manage-partners' => 'Manage partners',
            'view-form-submissions' => 'View form submissions',
            
            // Product permissions
            'manage-products' => 'Manage products and categories',
            
            // User management permissions
            'manage-users' => 'Manage users',
            'manage-roles' => 'Manage roles and permissions',
            'manage-modules' => 'Manage modules and menu items',
            
            // Settings permissions
            'manage-settings' => 'Manage system settings',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles and assign permissions
        // Web guard roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
        
        // API guard roles
        $apiAdminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $apiAdminRole->syncPermissions(Permission::where('guard_name', 'api')->get());

        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorPermissions = [
            'view-dashboard',
            'manage-content',
            'manage-company-info',
            'manage-press-releases',
            'manage-partners',
            'view-form-submissions',
            'manage-products',
        ];
        $editorRole->syncPermissions($editorPermissions);
        
        // API editor role
        $apiEditorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'api']);
        $apiEditorRole->syncPermissions(Permission::where('guard_name', 'api')
            ->whereIn('name', $editorPermissions)
            ->get());

        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewerPermissions = [
            'view-dashboard',
            'view-form-submissions',
        ];
        $viewerRole->syncPermissions($viewerPermissions);
        
        // API viewer role
        $apiViewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'api']);
        $apiViewerRole->syncPermissions(Permission::where('guard_name', 'api')
            ->whereIn('name', $viewerPermissions)
            ->get());

        // Create menu items
        // Dashboard menu items
        $dashboardMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $dashboardModule->id,
                'url' => '/admin/dashboard'
            ],
            [
                'name' => 'Dashboard',
                'icon' => 'dashboard',
                'order' => 1,
                'is_active' => true,
            ]
        );
        $dashboardMenuItem->permissions()->sync([Permission::where('name', 'view-dashboard')->first()->id]);

        // Content menu items
        $contentMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $contentModule->id,
                'url' => '/admin/content'
            ],
            [
                'name' => 'Content',
                'icon' => 'content_paste',
                'order' => 1,
                'is_active' => true,
        ]);
        $contentMenuItem->permissions()->sync([Permission::where('name', 'manage-content')->first()->id]);

        $companyInfoMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $contentModule->id,
                'url' => '/admin/company-info',
                'parent_id' => $contentMenuItem->id
            ],
            [
                'name' => 'Company Info',
                'icon' => 'business',
                'order' => 2,
                'is_active' => true,
            ]
        );
        $companyInfoMenuItem->permissions()->sync([Permission::where('name', 'manage-company-info')->first()->id]);

        $heroSlidesMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $contentModule->id,
                'url' => '/admin/hero-slides',
                'parent_id' => $contentMenuItem->id
            ],
            [
                'name' => 'Hero Slides',
                'icon' => 'slideshow',
                'order' => 3,
                'is_active' => true,
            ]
        );
        $heroSlidesMenuItem->permissions()->sync([Permission::where('name', 'manage-content')->first()->id]);

        $pressReleasesMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $contentModule->id,
                'url' => '/admin/press-releases',
                'parent_id' => $contentMenuItem->id
            ],
            [
                'name' => 'Press Releases',
                'icon' => 'article',
                'order' => 4,
                'is_active' => true,
            ]
        );
        $pressReleasesMenuItem->permissions()->sync([Permission::where('name', 'manage-press-releases')->first()->id]);

        $partnersMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $contentModule->id,
                'url' => '/admin/partners',
                'parent_id' => $contentMenuItem->id
            ],
            [
                'name' => 'Partners',
                'icon' => 'handshake',
                'order' => 5,
                'is_active' => true,
            ]
        );
        $partnersMenuItem->permissions()->sync([Permission::where('name', 'manage-partners')->first()->id]);

        $formSubmissionsMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $contentModule->id,
                'url' => '/admin/form-submissions',
                'parent_id' => $contentMenuItem->id
            ],
            [
                'name' => 'Form Submissions',
                'icon' => 'contact_mail',
                'order' => 6,
                'is_active' => true,
            ]
        );
        $formSubmissionsMenuItem->permissions()->sync([Permission::where('name', 'view-form-submissions')->first()->id]);

        // Product menu items
        $productMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $productModule->id,
                'url' => '/admin/products'
            ],
            [
                'name' => 'Products',
                'icon' => 'inventory_2',
                'order' => 1,
                'is_active' => true,
            ]
        );
        $productMenuItem->permissions()->sync([Permission::where('name', 'manage-products')->first()->id]);

        $productCategoriesMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $productModule->id,
                'url' => '/admin/product-categories',
                'parent_id' => $productMenuItem->id
            ],
            [
                'name' => 'Product Categories',
                'icon' => 'category',
                'order' => 2,
                'is_active' => true,
            ]
        );
        $productCategoriesMenuItem->permissions()->sync([Permission::where('name', 'manage-products')->first()->id]);

        // User management menu items
        $userMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $userModule->id,
                'url' => '/admin/users'
            ],
            [
                'name' => 'User Management',
                'icon' => 'people',
                'order' => 1,
                'is_active' => true,
            ]
        );
        $userMenuItem->permissions()->sync([Permission::where('name', 'manage-users')->first()->id]);

        $usersMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $userModule->id,
                'url' => '/admin/users',
                'parent_id' => $userMenuItem->id
            ],
            [
                'name' => 'Users',
                'icon' => 'person',
                'order' => 1,
                'is_active' => true,
            ]
        );
        $usersMenuItem->permissions()->sync([Permission::where('name', 'manage-users')->first()->id]);

        $rolesMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $userModule->id,
                'url' => '/admin/roles',
                'parent_id' => $userMenuItem->id
            ],
            [
                'name' => 'Roles',
                'icon' => 'admin_panel_settings',
                'order' => 2,
                'is_active' => true,
            ]
        );
        $rolesMenuItem->permissions()->sync([Permission::where('name', 'manage-roles')->first()->id]);

        $permissionsMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $userModule->id,
                'url' => '/admin/permissions',
                'parent_id' => $userMenuItem->id
            ],
            [
                'name' => 'Permissions',
                'icon' => 'security',
                'order' => 3,
                'is_active' => true,
            ]
        );
        $permissionsMenuItem->permissions()->sync([Permission::where('name', 'manage-roles')->first()->id]);

        $modulesMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $userModule->id,
                'url' => '/admin/modules',
                'parent_id' => $userMenuItem->id
            ],
            [
                'name' => 'Modules',
                'icon' => 'view_module',
                'order' => 4,
                'is_active' => true,
            ]
        );
        $modulesMenuItem->permissions()->sync([Permission::where('name', 'manage-modules')->first()->id]);

        $menuItemsMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $userModule->id,
                'url' => '/admin/menu-items',
                'parent_id' => $userMenuItem->id
            ],
            [
                'name' => 'Menu Items',
                'icon' => 'menu',
                'order' => 5,
                'is_active' => true,
            ]
        );
        $menuItemsMenuItem->permissions()->sync([Permission::where('name', 'manage-modules')->first()->id]);

        // Settings menu items
        $settingsMenuItem = MenuItem::firstOrCreate(
            [
                'module_id' => $settingsModule->id,
                'url' => '/admin/settings'
            ],
            [
                'name' => 'Settings',
                'icon' => 'settings',
                'order' => 1,
                'is_active' => true,
            ]
        );
        $settingsMenuItem->permissions()->sync([Permission::where('name', 'manage-settings')->first()->id]);

        // Assign admin role to first user if exists
        $user = User::first();
        if ($user) {
            $user->assignRole('admin'); // Assigns web guard role
            
            // Also assign API guard role
            // This is needed because Laravel Passport uses the API guard
            $apiAdminRole = Role::where('name', 'admin')->where('guard_name', 'api')->first();
            if ($apiAdminRole) {
                $user->assignRole($apiAdminRole);
            }
        }
    }
}