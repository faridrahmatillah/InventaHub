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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        // restrictOnDelete: kategori tidak boleh dihapus selama masih punya produk
        $table->foreignId('category_id')->constrained()->restrictOnDelete(); // Relasi ke tabel categories
        $table->string('code')->unique(); // Kode SKU / Barcode barang
        $table->string('name'); // Nama barang
        $table->integer('stock')->default(0); // Jumlah stok
        $table->decimal('price', 15, 2); // Harga barang
        $table->text('description')->nullable(); // Keterangan tambahan
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
