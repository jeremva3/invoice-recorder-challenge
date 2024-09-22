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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('voucher_series')->nullable(); // Voucher series
            $table->string('voucher_number')->nullable(); // Voucher number
            $table->string('voucher_type')->nullable(); // Voucher type
            $table->string('currency', 3)->nullable(); // Currency (USD, PEN)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('voucher_series');
            $table->dropColumn('voucher_number');
            $table->dropColumn('voucher_type');
            $table->dropColumn('currency');
        });
    }
};
