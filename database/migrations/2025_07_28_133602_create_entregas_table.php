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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empacotamento_id')->constrained('empacotamento')->onDelete('cascade');
            $table->foreignId('motorista_saida_id')->nullable()->constrained('usuarios'); // Motorista que confirmou saída
            $table->foreignId('motorista_entrega_id')->nullable()->constrained('usuarios'); // Motorista que confirmou entrega
            $table->foreignId('status_id')->constrained('status');
            $table->datetime('data_saida')->nullable();
            $table->datetime('data_entrega')->nullable();
            $table->datetime('data_confirmacao_recebimento')->nullable();
            $table->string('nome_recebedor')->nullable();
            $table->text('assinatura_recebedor')->nullable(); // Assinatura do motorista/recebedor
            $table->text('assinatura_cliente')->nullable(); // Assinatura do cliente final
            $table->text('observacoes_entrega')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
