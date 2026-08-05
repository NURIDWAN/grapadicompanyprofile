<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permission prefixes generated per resource.
     *
     * Must stay in sync with config('filament-shield.permission_prefixes.resource'),
     * otherwise policies will check permissions that were never created.
     *
     * @var list<string>
     */
    private const ACTIONS = [
        'view',
        'view_any',
        'create',
        'update',
        'restore',
        'restore_any',
        'replicate',
        'reorder',
        'delete',
        'delete_any',
        'force_delete',
        'force_delete_any',
    ];

    /**
     * Resources that are not auto-discovered from app/Filament/Resources,
     * but are still guarded by a policy (Filament Shield's own resources).
     *
     * @var list<string>
     */
    private const EXTRA_RESOURCES = [
        'role',
        'permission',
    ];

    /**
     * Permissions for custom pages, which follow no resource naming convention.
     *
     * @var list<string>
     */
    private const PAGE_PERMISSIONS = [
        'view_dashboard',
        'view_site_settings',
        'update_site_settings',
    ];

    /**
     * Resource identifiers that only super_admin may manage.
     *
     * @var list<string>
     */
    private const SUPER_ADMIN_ONLY_RESOURCES = [
        'user',
        'role',
        'permission',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $resources = $this->resourceIdentifiers();

        $resourcePermissions = [];
        foreach ($resources as $resource) {
            foreach (self::ACTIONS as $action) {
                $resourcePermissions[] = "{$action}_{$resource}";
            }
        }

        $allPermissions = [...$resourcePermissions, ...self::PAGE_PERMISSIONS];

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Drop permissions created under the older hyphenated naming convention.
        // They can never be satisfied by a policy, so they only add noise to the
        // Shield UI. Anything else (e.g. hand-made permissions) is left untouched.
        $stale = Permission::where('guard_name', 'web')
            ->whereIn('name', $this->legacyPermissionNames($resources))
            ->pluck('name');

        if ($stale->isNotEmpty()) {
            Permission::where('guard_name', 'web')->whereIn('name', $stale)->delete();
        }

        // Super Admin - every permission
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);

        // Admin - everything except user, role and permission management
        $adminPermissions = array_values(array_filter(
            $allPermissions,
            fn (string $permission): bool => ! $this->isSuperAdminOnly($permission),
        ));

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($adminPermissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->seedDefaultUsers();

        $this->command->info('Permissions and Roles seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['super_admin', count($allPermissions)],
                ['admin', count($adminPermissions)],
            ]
        );

        if ($stale->isNotEmpty()) {
            $this->command->warn("Removed {$stale->count()} stale permission(s): ".$stale->implode(', '));
        }
    }

    /**
     * Derive resource permission identifiers from the Filament resource classes.
     *
     * Mirrors Filament Shield's own convention, where the resource class name is
     * snake_cased and underscores become "::" (AssetCategoryResource -> asset::category).
     *
     * @return list<string>
     */
    private function resourceIdentifiers(): array
    {
        $identifiers = [];

        foreach (glob(app_path('Filament/Resources/*.php')) ?: [] as $path) {
            $identifiers[] = (string) Str::of(basename($path, '.php'))
                ->beforeLast('Resource')
                ->snake()
                ->replace('_', '::');
        }

        $identifiers = [...$identifiers, ...self::EXTRA_RESOURCES];

        sort($identifiers);

        return array_values(array_unique($identifiers));
    }

    /**
     * Hyphenated variants of the canonical permission names, e.g.
     * "create_company-timeline" for "create_company::timeline".
     *
     * @param  list<string>  $resources
     * @return list<string>
     */
    private function legacyPermissionNames(array $resources): array
    {
        $legacy = [];

        foreach ($resources as $resource) {
            if (! str_contains($resource, '::')) {
                continue;
            }

            $legacyResource = str_replace('::', '-', $resource);

            foreach (self::ACTIONS as $action) {
                $legacy[] = "{$action}_{$legacyResource}";
            }
        }

        return $legacy;
    }

    private function isSuperAdminOnly(string $permission): bool
    {
        foreach (self::SUPER_ADMIN_ONLY_RESOURCES as $resource) {
            if (Str::endsWith($permission, '_'.$resource)) {
                return true;
            }
        }

        return false;
    }

    private function seedDefaultUsers(): void
    {
        $defaults = [
            ['email' => 'superadmin@grapadi.com', 'name' => 'Super Admin', 'role' => 'super_admin'],
            ['email' => 'admin@grapadi.com', 'name' => 'Admin', 'role' => 'admin'],
        ];

        foreach ($defaults as $default) {
            $user = User::firstOrCreate(
                ['email' => $default['email']],
                [
                    'name' => $default['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // Existing accounts may predate the is_active column default.
            if (! $user->is_active) {
                $user->forceFill(['is_active' => true])->save();
            }

            $user->syncRoles([$default['role']]);
        }

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
