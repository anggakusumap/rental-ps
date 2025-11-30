<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add customer_id to rental_sessions (with foreign key constraint)
        Schema::table('rental_sessions', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('package_id')->constrained()->nullOnDelete();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('total_cost');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });

        // Add customer_id to orders (with foreign key constraint)
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('rental_session_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rental_sessions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'payment_status', 'payment_method', 'paid_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
