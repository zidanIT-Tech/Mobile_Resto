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
        Schema::create('orderans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_konsumen');
            $table->integer('total_bayar');
            $table->dateTime('tanggal_orderan');
            $table->enum('status', ['pending', 'dibayar', 'batal']);
            $table->foreignId('id_user')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('id_meja')->nullable()->constrained('mejas')->nullOnDelete();
            $table->enum('metode_pembayaran', ['cash', 'qris', 'cashless']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderans');
    }
};
