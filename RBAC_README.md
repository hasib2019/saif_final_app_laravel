# Role-Based Access Control System

This document provides an overview of the Role-Based Access Control (RBAC) system implemented in this application.

## Overview

The RBAC system provides a flexible way to manage user permissions through roles, modules, and menu items. It allows for:

- Creating and managing roles with specific permissions
- Organizing the application into modules
- Creating menu items within modules
- Assigning permissions to menu items
- Dynamically generating navigation menus based on user permissions
- URL-based permission checking

## Components

### Models

1. **Module**: Represents a section of the application (e.g., Dashboard, Content Management, Products)
   - Fields: name, description, icon, order, is_active

2. **MenuItem**: Represents a menu item in the navigation
   - Fields: module_id, name, url, icon, order, is_active, parent_id
   - Relationships: module, parent, children, permissions

3. **Role**: Represents a user role (e.g., admin, editor, viewer)
   - Provided by Spatie's Laravel Permission package

4. **Permission**: Represents a specific permission (e.g., manage-content, view-dashboard)
   - Provided by Spatie's Laravel Permission package

### Controllers

1. **ModuleController**: Manages modules
2. **MenuItemController**: Manages menu items
3. **RoleController**: Manages roles
4. **PermissionController**: Manages permissions
5. **MenuController**: Provides the menu structure for the frontend based on user permissions

### Middleware

1. **CheckMenuItemPermission**: Checks if a user has permission to access a specific URL

## Installation

To install the RBAC system, run the following command:

```bash
php artisan rbac:install
```

This command will:

1. Run the necessary migrations
2. Seed the database with default roles, permissions, modules, and menu items
3. Clear the application cache

## Default Roles

The system comes with three default roles:

1. **admin**: Has access to all features
2. **editor**: Has access to content management and products
3. **viewer**: Has limited access to dashboard and form submissions

## Usage

### Getting the User Menu

To get the menu structure for the authenticated user based on their permissions, make a GET request to:

```
/api/menu
```

This will return a JSON response with the modules and menu items the user has access to.

### Managing Roles

Roles can be managed through the following endpoints:

```
GET    /api/admin/roles            # List all roles
POST   /api/admin/roles            # Create a new role
GET    /api/admin/roles/{id}       # Get a specific role
PUT    /api/admin/roles/{id}       # Update a role
DELETE /api/admin/roles/{id}       # Delete a role
```

### Managing Permissions

Permissions can be managed through the following endpoints:

```
GET    /api/admin/permissions            # List all permissions
POST   /api/admin/permissions            # Create a new permission
GET    /api/admin/permissions/grouped    # Get permissions grouped by module
GET    /api/admin/permissions/{id}       # Get a specific permission
PUT    /api/admin/permissions/{id}       # Update a permission
DELETE /api/admin/permissions/{id}       # Delete a permission
```

### Managing Modules

Modules can be managed through the following endpoints:

```
GET    /api/admin/modules            # List all modules
POST   /api/admin/modules            # Create a new module
GET    /api/admin/modules/{id}       # Get a specific module
PUT    /api/admin/modules/{id}       # Update a module
DELETE /api/admin/modules/{id}       # Delete a module
```

### Managing Menu Items

Menu items can be managed through the following endpoints:

```
GET    /api/admin/menu-items            # List all menu items
POST   /api/admin/menu-items            # Create a new menu item
GET    /api/admin/menu-items/{id}       # Get a specific menu item
PUT    /api/admin/menu-items/{id}       # Update a menu item
DELETE /api/admin/menu-items/{id}       # Delete a menu item
```

## Frontend Integration

The frontend can use the `/api/menu` endpoint to dynamically generate the navigation menu based on the user's permissions. This ensures that users only see menu items they have access to.

## URL-Based Permission Checking

The system includes a middleware (`menu_permission`) that checks if a user has permission to access a specific URL. This middleware is applied to all admin routes.

When a user tries to access a URL, the middleware checks if there is a menu item with that URL and if the user has the required permissions to access it.

## Extending the System

To add new modules, menu items, roles, or permissions, you can either use the provided API endpoints or modify the `RolePermissionSeeder` class and run the seeder again.

## Best Practices

1. **Naming Conventions**: Use consistent naming conventions for permissions (e.g., `manage-content`, `view-dashboard`)
2. **Module Organization**: Organize your application into logical modules
3. **Menu Structure**: Create a clear and intuitive menu structure
4. **Permission Assignment**: Assign permissions to menu items based on the actions they allow
5. **Role Creation**: Create roles based on user responsibilities

## Troubleshooting

If you encounter any issues with the RBAC system, try the following:

1. Clear the application cache: `php artisan cache:clear`
2. Clear the route cache: `php artisan route:clear`
3. Clear the config cache: `php artisan config:clear`
4. Clear the view cache: `php artisan view:clear`
5. Check the Laravel log file for any errors

## Conclusion

The RBAC system provides a flexible and powerful way to manage user permissions in your application. By organizing your application into modules and menu items, and assigning permissions to them, you can create a dynamic and secure user experience.