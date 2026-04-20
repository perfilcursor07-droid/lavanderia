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
        Schema::create('anotacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('modulo', 50); // estabelecimentos, coletas, empacotamento, etc.
            $table->string('pagina', 100)->nullable(); // URL específica da página
            $table->string('pagina_nome', 150)->nullable(); // Nome amigável da página
            $table->enum('categoria', ['melhorias', 'alteracoes', 'exclusoes']);
            $table->text('texto');
            $table->boolean('resolvida')->default(false);
            $table->timestamp('data_resolucao')->nullable();
            $table->text('observacao_resolucao')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['usuario_id', 'modulo']);
            $table->index(['modulo', 'categoria']);
            $table->index('resolvida');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anotacoes');
    }
};
