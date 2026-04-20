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
        Schema::table('coleta_pecas', function (Blueprint $table) {
            $table->dropColumn(['preco_unitario', 'subtotal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coleta_pecas', function (Blueprint $table) {
            $table->decimal('preco_unitario', 8, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
        });
    }
};
