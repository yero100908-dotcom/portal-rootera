<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('karyawan')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('photo')->nullable()->after('phone');
            $table->longText('face_descriptor')->nullable()->after('photo');
            $table->boolean('is_active')->default(true)->after('face_descriptor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'photo', 'face_descriptor', 'is_active']);
        });
    }
};
