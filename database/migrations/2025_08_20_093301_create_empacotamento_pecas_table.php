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
        Schema::create('empacotamento_pecas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empacotamento_id')->constrained('empacotamento')->onDelete('cascade');
            $table->foreignId('tipo_id')->constrained('tipos')->onDelete('cascade');
            $table->string('codigo_qr')->unique();
            $table->integer('quantidade');
            $table->decimal('peso', 8, 3)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['empacotamento_id', 'tipo_id']);
            $table->index('codigo_qr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empacotamento_pecas');
    }
};
