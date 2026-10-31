<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('system_stock');
            $table->unsignedInteger('counted_stock');
            $table->integer('difference');
            $table->date('counted_at');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'counted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
