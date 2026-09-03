<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Cashflow;
use App\Models\ContentCalendar;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $roles = ['owner', 'admin', 'mentor', 'karyawan', 'siswa'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Create Users for each role
        $owner = User::create([
            'name' => 'Pak Hendra (Owner)',
            'email' => 'owner@absensi.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'phone' => '081234567890',
            'is_active' => true,
        ]);
        $owner->assignRole('owner');

        $admin = User::create([
            'name' => 'Siti Admin (Admin Operasional)',
            'email' => 'admin@absensi.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081298765432',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $mentor = User::create([
            'name' => 'Budi Santoso (Mentor Digital)',
            'email' => 'mentor@absensi.com',
            'password' => Hash::make('password123'),
            'role' => 'mentor',
            'phone' => '081377778888',
            'is_active' => true,
        ]);
        $mentor->assignRole('mentor');

        $karyawan = User::create([
            'name' => 'Rian Pratama (Staff Marketing)',
            'email' => 'karyawan@absensi.com',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'phone' => '081511112222',
            'is_active' => true,
        ]);
        $karyawan->assignRole('karyawan');

        $siswa = User::create([
            'name' => 'Anisa Rahma (Siswa Magang)',
            'email' => 'siswa@absensi.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'phone' => '081933334444',
            'is_active' => true,
        ]);
        $siswa->assignRole('siswa');

        // 3. Seed Sample Orders
        $order1 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-1001',
            'client_name' => 'PT Synergy Digital Indonesia',
            'client_phone' => '081200001111',
            'details' => 'Jasa Pembuatan Website Enterprise & Branding Kit',
            'total_amount' => 15000000.00,
            'transaction_date' => now()->subDays(5),
            'payment_status' => 'lunas',
            'created_by' => $admin->id,
        ]);

        $order2 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-1002',
            'client_name' => 'CV Nusantara Creative',
            'client_phone' => '081200002222',
            'details' => 'Paket Social Media Management 3 Bulan',
            'total_amount' => 7500000.00,
            'transaction_date' => now()->subDays(2),
            'payment_status' => 'dp',
            'created_by' => $admin->id,
        ]);

        // 4. Seed Sample Cashflows
        Cashflow::create([
            'type' => 'inflow',
            'order_id' => $order1->id,
            'amount' => 15000000.00,
            'date' => now()->subDays(5),
            'source_destination' => 'Pelunasan Order #ORD-1001 (PT Synergy Digital)',
            'category' => 'Penjualan / Order Client',
            'account_number' => 'BCA 8830123999',
            'note' => 'Transfer via Bank Mandiri',
            'created_by' => $admin->id,
        ]);

        Cashflow::create([
            'type' => 'inflow',
            'order_id' => $order2->id,
            'amount' => 3750000.00,
            'date' => now()->subDays(2),
            'source_destination' => 'DP Order #ORD-1002 (CV Nusantara)',
            'category' => 'Penjualan / Order Client',
            'account_number' => 'BCA 8830123999',
            'note' => 'DP 50%',
            'created_by' => $admin->id,
        ]);

        Cashflow::create([
            'type' => 'outflow',
            'amount' => 4500000.00,
            'date' => now()->subDays(4),
            'source_destination' => 'Honorarium Mentor Bulan Ini',
            'category' => 'Gaji Mentor / Staff',
            'account_number' => 'BCA 8830123999',
            'note' => 'Pembayaran gaji Budi Santoso',
            'created_by' => $admin->id,
        ]);

        Cashflow::create([
            'type' => 'outflow',
            'amount' => 1200000.00,
            'date' => now()->subDays(1),
            'source_destination' => 'Cloud Server & Domain Renewal',
            'category' => 'Biaya Server & Cloud',
            'account_number' => 'Credit Card',
            'note' => 'Tagihan AWS & Vultr',
            'created_by' => $admin->id,
        ]);

        // 5. Seed Content Calendars
        ContentCalendar::create([
            'title' => 'Tips Membangun Personal Branding di LinkedIn 2026',
            'brief_copywriting' => 'Carousel post 5 slide membahas strategi linkedin networking',
            'platform' => 'Instagram',
            'status' => 'published',
            'scheduled_at' => now()->subDays(1)->setTime(10, 0),
            'assigned_to' => $mentor->id,
            'created_by' => $admin->id,
        ]);

        ContentCalendar::create([
            'title' => 'Tutorial Setup Laravel 12 Best Practices',
            'brief_copywriting' => 'Video reel 60 detik demo pembuatan laravel setup cepat',
            'platform' => 'TikTok',
            'status' => 'scheduled',
            'scheduled_at' => now()->addDays(1)->setTime(14, 0),
            'assigned_to' => $mentor->id,
            'created_by' => $admin->id,
        ]);

        ContentCalendar::create([
            'title' => 'Behind the Scenes Office Culture & Internship Day',
            'brief_copywriting' => 'Vlog singkat keceriaan magang dan mentoring',
            'platform' => 'YouTube',
            'status' => 'production',
            'scheduled_at' => now()->addDays(3)->setTime(19, 0),
            'assigned_to' => $mentor->id,
            'created_by' => $admin->id,
        ]);

        // 6. Seed Sample Attendance Logs
        Attendance::create([
            'user_id' => $karyawan->id,
            'waktu_presensi' => now()->subDays(1)->setTime(7, 52),
            'tipe' => 'masuk',
            'status' => 'tepat_waktu',
            'catatan' => 'Presensi via Face Recognition Webcam',
            'is_verified' => true,
        ]);

        Attendance::create([
            'user_id' => $karyawan->id,
            'waktu_presensi' => now()->subDays(1)->setTime(17, 05),
            'tipe' => 'pulang',
            'status' => 'tepat_waktu',
            'catatan' => 'Presensi pulang tepat waktu',
            'is_verified' => true,
        ]);

        Attendance::create([
            'user_id' => $siswa->id,
            'waktu_presensi' => now()->setTime(8, 15),
            'tipe' => 'masuk',
            'status' => 'terlambat',
            'catatan' => 'Macet lalu lintas',
            'is_verified' => true,
        ]);
    }
}
