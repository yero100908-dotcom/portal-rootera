<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Cashflow;
use App\Models\ContentCalendar;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with real team hierarchy & reset transactions to ZERO state.
     */
    public function run(): void
    {
        // 1. Seed Real Users Hierarchy
        $this->call(UserSeeder::class);

        // Remove old demo accounts if present
        User::whereNotIn('email', [
            'owner@absensi.com',
            'mentor@absensi.com',
            'admin@absensi.com',
            'karyawan@absensi.com',
            'desty@absensi.com',
            'prayoga@absensi.com',
            'maura@absensi.com',
            'rafa@absensi.com',
            'ghaitsaa@absensi.com',
        ])->delete();

        // 2. Reset Financial, Order, Content, and Attendance tables to ZERO STATE
        Schema::disableForeignKeyConstraints();
        Cashflow::truncate();
        Order::truncate();
        ContentCalendar::truncate();
        Attendance::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
