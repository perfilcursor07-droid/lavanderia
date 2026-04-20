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
        Schema::create('pesagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleta_id')->constrained('coletas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios'); // Responsável pela pesagem
            $table->foreignId('tipo_id')->constrained('tipos'); // Tipo de peça pesada
            $table->decimal('peso', 8, 2); // Peso da peça em kg
            $table->integer('quantidade')->default(1); // Quantidade de peças pesadas
            $table->decimal('peso_unitario', 8, 2)->nullable(); // Peso unitário calculado
            $table->datetime('data_pesagem'); // Data e hora da pesagem
            $table->text('observacoes')->nullable(); // Observações sobre a pesagem
            $table->string('local_pesagem')->nullable(); // Local onde foi feita a pesagem
            $table->boolean('conferido')->default(false); // Se a pesagem foi conferida
            $table->foreignId('usuario_conferencia_id')->nullable()->constrained('usuarios'); // Usuário que conferiu
            $table->datetime('data_conferencia')->nullable(); // Data da conferência
            $table->timestamps();

            // Índices para melhor performance
            $table->index(['coleta_id', 'tipo_id']);
            $table->index('data_pesagem');
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesagens');
    }
};
