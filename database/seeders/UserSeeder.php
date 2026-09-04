<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users table with the real team hierarchy.
     */
    public function run(): void
    {
        // 1. Ensure Spatie Roles Exist
        $roles = ['owner', 'admin', 'mentor', 'karyawan', 'siswa'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Real Team Hierarchy Data (Preserving existing auth/credential structure & default password)
        $usersData = [
            // MANAGEMENT & ADMIN
            [
                'name' => 'Wibowo Pratikno',
                'email' => 'owner@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone' => '081234567890',
                'is_active' => true,
            ],
            [
                'name' => 'Rafael Abimanyu',
                'email' => 'mentor@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'mentor',
                'phone' => '081377778888',
                'is_active' => true,
            ],
            [
                'name' => 'Mono',
                'email' => 'admin@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081298765432',
                'is_active' => true,
            ],

            // STAFF & KARYAWAN
            [
                'name' => 'Yero Virdhan Akifan',
                'email' => 'karyawan@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'phone' => '081511112222',
                'is_active' => true,
            ],
            [
                'name' => 'Desty Mikayla Ariana',
                'email' => 'desty@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'phone' => '081511112223',
                'is_active' => true,
            ],
            [
                'name' => 'Prayoga Satya Kelana',
                'email' => 'prayoga@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'phone' => '081511112224',
                'is_active' => true,
            ],
            [
                'name' => 'Maura Aurellia',
                'email' => 'maura@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'phone' => '081511112225',
                'is_active' => true,
            ],
            [
                'name' => 'Muhammad Rafa Raditya',
                'email' => 'rafa@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'phone' => '081511112226',
                'is_active' => true,
            ],
            [
                'name' => 'Ghaitsaa Kahla',
                'email' => 'ghaitsaa@absensi.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081511112227',
                'is_active' => true,
            ],
        ];

        foreach ($usersData as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );
            $user->syncRoles([$u['role']]);
        }
    }
}
