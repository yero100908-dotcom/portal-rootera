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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('waktu_presensi');
            $table->enum('tipe', ['masuk', 'pulang']);
            $table->enum('status', ['tepat_waktu', 'terlambat', 'izin', 'sakit', 'alpa'])->default('tepat_waktu');
            $table->text('catatan')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'waktu_presensi']);
            $table->index(['tipe', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
