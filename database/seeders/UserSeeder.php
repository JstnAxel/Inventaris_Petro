<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Role yang sudah ada, jangan dihapus
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Buat Permission baru
        $viewAsset = Permission::firstOrCreate(['name' => 'view asset']);
        $viewStationary = Permission::firstOrCreate(['name' => 'view stationary']);
        $viewBoth = Permission::firstOrCreate(['name' => 'view both']);

        // Buat user admin, assign role admin dan permission view both (misal)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'department' => 'IT' ]
        );
        $admin->assignRole($adminRole);
        $admin->syncPermissions([$viewBoth]); // Bisa juga assign permission lain sesuai kebutuhan

        // User A hanya bisa view asset
        $userAsset = User::firstOrCreate(
            ['email' => 'user-asset@example.com'],
            ['name' => 'User Asset', 'password' => bcrypt('password'), 'department' => 'Umum' ]
        );
        $userAsset->assignRole($userRole);
        $userAsset->syncPermissions([$viewAsset]);

        // User B hanya bisa view stationary
        $userStationary = User::firstOrCreate(
            ['email' => 'user-stationary@example.com'],
            ['name' => 'User Stationary', 'password' => bcrypt('password'), 'department' => 'AUDIT' ]
        );
        $userStationary->assignRole($userRole);
        $userStationary->syncPermissions([$viewStationary]);

        // User C bisa view keduanya
        $userBoth = User::firstOrCreate(
            ['email' => 'user-both@example.com'],
            ['name' => 'User Both', 'password' => bcrypt('password'), 'department' => 'SDM' ]
        );
        $userBoth->assignRole($userRole);
        $userBoth->syncPermissions([$viewBoth]);

        // Jika ingin, bisa assign permission individual juga secara kombinasi
        // Misal: $userBoth->syncPermissions([$viewAsset, $viewStationary]);
    }
}
