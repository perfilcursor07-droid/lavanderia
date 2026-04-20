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
        Schema::table('estabelecimentos', function (Blueprint $table) {
            // Renomear campos antigos
            $table->renameColumn('email', 'email_old');
            $table->renameColumn('contato_responsavel', 'contato_responsavel_old');
        });

        Schema::table('estabelecimentos', function (Blueprint $table) {
            // Adicionar novos campos JSON
            $table->json('emails')->nullable();
            $table->json('contatos_responsaveis')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estabelecimentos', function (Blueprint $table) {
            // Remover campos JSON
            $table->dropColumn(['emails', 'contatos_responsaveis']);
        });

        Schema::table('estabelecimentos', function (Blueprint $table) {
            // Restaurar campos antigos
            $table->renameColumn('email_old', 'email');
            $table->renameColumn('contato_responsavel_old', 'contato_responsavel');
        });
    }
};
