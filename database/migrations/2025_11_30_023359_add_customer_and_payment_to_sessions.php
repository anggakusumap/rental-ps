<?php
// File: database/migrations/2025_11_30_023359_add_customer_and_payment_to_sessions.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add customer_id to rental_sessions (with index, no foreign key)
        Schema::table('rental_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();
        });

        // Add customer_id to orders (with index, no foreign key)
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();
        });

        // Add payment fields to rental_sessions
        Schema::table('rental_sessions', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('total_cost');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('rental_sessions', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropColumn(['customer_id', 'payment_status', 'payment_method', 'paid_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
