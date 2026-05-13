<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'transaksi_penjualan',
            'transaksi_penjualan_detail',
            'pembayaran_penjualan',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->string('deleted_by', 50)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'transaksi_penjualan',
            'transaksi_penjualan_detail',
            'pembayaran_penjualan',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->dropColumn('deleted_at');
                }

                if (Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->dropColumn('deleted_by');
                }
            });
        }
    }
};