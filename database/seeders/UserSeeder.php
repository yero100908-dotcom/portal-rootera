<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users table with the official credentials matrix.
     */
    public function run(): void
    {
        // 1. Ensure Spatie Roles Exist
        $roles = ['owner', 'admin', 'mentor', 'it', 'sosmed', 'cs', 'karyawan', 'siswa'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Official Credentials Matrix Data
        $usersData = [
            // OWNER
            [
                'name' => 'Wibowo Pratikno',
                'email' => 'wibowo@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone' => '081234567890',
                'is_active' => true,
            ],
            // MENTOR
            [
                'name' => 'Rafael Abimanyu',
                'email' => 'rafael@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'mentor',
                'phone' => '081377778888',
                'is_active' => true,
            ],
            // ADMIN
            [
                'name' => 'Mono',
                'email' => 'mono@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081298765432',
                'is_active' => true,
            ],
            // IT STAFF
            [
                'name' => 'Yero Virdhan Akifan',
                'email' => 'yero@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'it',
                'phone' => '081511112222',
                'is_active' => true,
            ],
            [
                'name' => 'Desty Mikayla Ariana',
                'email' => 'desty@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'it',
                'phone' => '081511112223',
                'is_active' => true,
            ],
            // SOCIAL MEDIA STAFF
            [
                'name' => 'Prayoga Satya Kelana',
                'email' => 'prayoga@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'sosmed',
                'phone' => '081511112224',
                'is_active' => true,
            ],
            [
                'name' => 'Maura Aurellia',
                'email' => 'maura@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081511112225',
                'is_active' => true,
            ],
            [
                'name' => 'Muhammad Rafa Raditya',
                'email' => 'rafa@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'sosmed',
                'phone' => '081511112226',
                'is_active' => true,
            ],
            // CUSTOMER SERVICE / ADMIN STAFF
            [
                'name' => 'Ghaitsaa Kahla',
                'email' => 'ghaitsaa@smartattend.com',
                'password' => Hash::make('password123'),
                'role' => 'cs',
                'phone' => '081511112227',
                'is_active' => true,
            ],
        ];

        foreach ($usersData as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );

            if ($u['email'] === 'maura@smartattend.com') {
                $user->syncRoles(['admin', 'sosmed', 'cs']);
            } else {
                $user->syncRoles([$u['role']]);
            }
        }

        // Seed legacy email aliases if needed for test backward-compatibility
        $legacyAliases = [
            ['email' => 'owner@absensi.com', 'target_email' => 'wibowo@smartattend.com', 'role' => 'owner'],
            ['email' => 'admin@absensi.com', 'target_email' => 'mono@smartattend.com', 'role' => 'admin'],
            ['email' => 'mentor@absensi.com', 'target_email' => 'rafael@smartattend.com', 'role' => 'mentor'],
            ['email' => 'karyawan@absensi.com', 'target_email' => 'yero@smartattend.com', 'role' => 'it'],
        ];

        foreach ($legacyAliases as $alias) {
            $targetUser = User::where('email', $alias['target_email'])->first();
            if ($targetUser) {
                User::updateOrCreate(
                    ['email' => $alias['email']],
                    [
                        'name' => $targetUser->name,
                        'email' => $alias['email'],
                        'password' => Hash::make('password123'),
                        'role' => $alias['role'],
                        'phone' => $targetUser->phone,
                        'is_active' => true,
                    ]
                )->syncRoles([$alias['role']]);
            }
        }
    }
}
