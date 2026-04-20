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
        Schema::create('coletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estabelecimento_id')->constrained('estabelecimentos');
            $table->foreignId('usuario_id')->constrained('usuarios'); // Usuário que registrou a coleta
            $table->foreignId('status_id')->constrained('status');
            $table->datetime('data_agendamento');
            $table->datetime('data_coleta')->nullable();
            $table->datetime('data_conclusao')->nullable();
            $table->text('observacoes')->nullable();
            $table->text('motivo_cancelamento')->nullable();
            $table->decimal('peso_total', 8, 2)->default(0);
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->string('numero_coleta')->unique(); // Número sequencial da coleta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coletas');
    }
};
