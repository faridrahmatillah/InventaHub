<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['supplier_id', 'user_id']);
        });
    }
};
