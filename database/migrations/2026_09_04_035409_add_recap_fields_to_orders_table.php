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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('lead_source')->nullable()->after('client_phone');
            $table->decimal('commission_rate', 5, 2)->default(0)->after('total_amount');
            $table->decimal('commission_amount', 15, 2)->default(0)->after('commission_rate');
            $table->foreignId('pic_user_id')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pic_user_id']);
            $table->dropColumn(['lead_source', 'commission_rate', 'commission_amount', 'pic_user_id']);
        });
    }
};
