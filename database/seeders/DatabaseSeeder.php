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
            'wibowo@smartattend.com',
            'rafael@smartattend.com',
            'mono@smartattend.com',
            'yero@smartattend.com',
            'desty@smartattend.com',
            'prayoga@smartattend.com',
            'maura@smartattend.com',
            'rafa@smartattend.com',
            'ghaitsaa@smartattend.com',
            'owner@absensi.com',
            'mentor@absensi.com',
            'admin@absensi.com',
            'karyawan@absensi.com',
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
