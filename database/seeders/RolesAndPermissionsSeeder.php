<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all resources and their CRUD actions
        $resources = [
            'article',
            'brand',
            'category',
            'client',
            'company-timeline',
            'executive-team',
            'interaction',
            'asset',
            'asset-category',
            'lead',
            'portfolio',
            'project',
            'service',
            'tag',
            'user',
        ];

        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'restore', 'restore_any', 'force_delete', 'force_delete_any'];

        // Create resource permissions
        $allPermissions = [];
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissionName = "{$action}_{$resource}";
                $allPermissions[] = $permissionName;
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }

        // Page permissions
        $pagePermissions = [
            'view_dashboard',
            'view_site_settings',
            'update_site_settings',
        ];

        foreach ($pagePermissions as $permissionName) {
            $allPermissions[] = $permissionName;
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Create roles - Only 2 roles: super_admin and admin
        // Super Admin - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);

        // Admin - has all permissions except user management
        $adminPermissions = array_filter($allPermissions, function ($perm) {
            return ! str_contains($perm, '_user');
        });
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($adminPermissions);

        // Create default users
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@grapadi.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@grapadi.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $this->command->info('Permissions and Roles seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['super_admin', count($allPermissions)],
                ['admin', count($adminPermissions)],
            ]
        );
        $this->command->info('Default users created:');
        $this->command->table(
            ['Email', 'Role', 'Password'],
            [
                ['superadmin@grapadi.com', 'super_admin', 'password123'],
                ['admin@grapadi.com', 'admin', 'password123'],
            ]
        );
    }
}
