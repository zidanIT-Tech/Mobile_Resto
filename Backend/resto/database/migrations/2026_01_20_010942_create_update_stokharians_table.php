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
        Schema::create('update_stokharians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_menu')->constrained('menus')->cascadeOnDelete();
            $table->integer('jumlah_porsi');
            $table->date('tanggal_update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_stokharians');
    }
};
