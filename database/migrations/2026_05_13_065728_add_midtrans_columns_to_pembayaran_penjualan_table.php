<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_penjualan', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran_penjualan', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'snap_token')) {
                $table->string('snap_token')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'payment_type')) {
                $table->string('payment_type')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'transaction_status')) {
                $table->string('transaction_status')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'fraud_status')) {
                $table->string('fraud_status')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'tanggal_pembayaran')) {
                $table->timestamp('tanggal_pembayaran')->nullable();
            }

            if (!Schema::hasColumn('pembayaran_penjualan', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_penjualan', function (Blueprint $table) {
            $columns = [
                'midtrans_order_id',
                'snap_token',
                'payment_type',
                'transaction_status',
                'fraud_status',
                'transaction_id',
                'tanggal_pembayaran',
                'updated_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('pembayaran_penjualan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};