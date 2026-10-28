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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel products (Syarat wajib UAS)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            // Jenis transaksi: 'in' (Masuk) atau 'out' (Keluar)
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
