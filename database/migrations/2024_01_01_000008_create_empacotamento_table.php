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
        Schema::create('empacotamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleta_id')->constrained('coletas');
            $table->foreignId('usuario_empacotamento_id')->constrained('usuarios'); // Usuário que fez o empacotamento
            $table->foreignId('motorista_id')->nullable()->constrained('usuarios'); // Motorista responsável
            $table->foreignId('status_id')->constrained('status');
            $table->string('codigo_qr')->unique(); // Código QR único
            $table->datetime('data_empacotamento');
            $table->datetime('data_saida')->nullable();
            $table->datetime('data_entrega')->nullable();
            $table->datetime('data_confirmacao_recebimento')->nullable();
            $table->string('assinatura_recebimento')->nullable(); // Path para arquivo de assinatura
            $table->string('nome_recebedor')->nullable();
            $table->text('observacoes_empacotamento')->nullable();
            $table->text('observacoes_entrega')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empacotamento');
    }
};
