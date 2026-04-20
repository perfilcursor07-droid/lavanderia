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
        // Tabela para rastrear etapas do empacotamento por funcionário
        Schema::create('empacotamento_etapas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empacotamento_id');
            $table->unsignedBigInteger('tipo_id');
            $table->unsignedBigInteger('usuario_responsavel_id');
            $table->enum('status', ['em_andamento', 'finalizado'])->default('em_andamento');
            $table->timestamp('data_inicio');
            $table->timestamp('data_finalizacao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['empacotamento_id', 'tipo_id']);
            $table->index(['usuario_responsavel_id', 'status']);
            $table->index('data_inicio');
            
            // Chaves estrangeiras
            $table->foreign('empacotamento_id')->references('id')->on('empacotamento')->onDelete('cascade');
            $table->foreign('tipo_id')->references('id')->on('tipos')->onDelete('cascade');
            $table->foreign('usuario_responsavel_id')->references('id')->on('usuarios')->onDelete('cascade');
        });

        // Adicionar campo para controle de status de tipos no empacotamento
        Schema::table('empacotamento', function (Blueprint $table) {
            $table->json('tipos_finalizados')->nullable()->comment('Array com IDs dos tipos que foram finalizados no empacotamento');
            $table->decimal('progresso_percentual', 5, 2)->default(0.00)->comment('Percentual de progresso do empacotamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empacotamento_etapas');
        
        Schema::table('empacotamento', function (Blueprint $table) {
            $table->dropColumn(['tipos_finalizados', 'progresso_percentual']);
        });
    }
};
