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
        Schema::create('coleta_pecas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleta_id')->constrained('coletas')->onDelete('cascade');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->integer('quantidade');
            $table->decimal('peso', 8, 2);
            $table->decimal('preco_unitario', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coleta_pecas');
    }
};
