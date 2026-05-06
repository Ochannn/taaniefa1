<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_user', 50)->nullable();
            $table->string('target_role', 50)->nullable();
            $table->string('tipe', 100);
            $table->string('judul', 150);
            $table->text('pesan');
            $table->text('url')->nullable();
            $table->string('ref_table', 100)->nullable();
            $table->string('ref_kode', 100)->nullable();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->index('kode_user');
            $table->index('target_role');
            $table->index('dibaca_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};