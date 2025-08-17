<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage-users',
            'manage-company-info',
            'manage-products',
            'manage-categories',
            'manage-press-releases',
            'manage-partners',
            'view-form-submissions',
            'manage-languages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);

        // Assign all permissions to admin
        $adminRole->syncPermissions($permissions);

        // Assign limited permissions to editor
        $editorRole->syncPermissions([
            'manage-company-info',
            'manage-products',
            'manage-categories',
            'manage-press-releases',
            'manage-partners',
            'view-form-submissions',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@derown.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        // Create editor user
        $editor = User::firstOrCreate(
            ['email' => 'editor@derown.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $editor->assignRole('editor');
    }
}
