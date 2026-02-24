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
        Schema::create('detail_orderans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_orderan')->constrained('orderans')->cascadeOnDelete();
            $table->foreignId('id_menu')->constrained('menus')->restrictOnDelete();
            $table->integer('jumlah');
            $table->enum('metode_pesanan', ['takeaway', 'dinein']);
            $table->text('catatan')->nullable();
            $table->enum('status', ['processing', 'done']);
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_orderans');
    }
};
