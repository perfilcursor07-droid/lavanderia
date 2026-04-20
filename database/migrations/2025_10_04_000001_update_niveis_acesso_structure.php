<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar permissões do Administrador
        DB::table('niveis_acesso')
            ->where('id', 1)
            ->update([
                'permissoes' => json_encode([
                    'usuarios.criar',
                    'usuarios.editar',
                    'usuarios.excluir',
                    'usuarios.visualizar',
                    'estabelecimentos.criar',
                    'estabelecimentos.editar',
                    'estabelecimentos.excluir',
                    'estabelecimentos.visualizar',
                    'coletas.criar',
                    'coletas.editar',
                    'coletas.cancelar',
                    'coletas.visualizar',
                    'pesagem.criar',
                    'pesagem.editar',
                    'pesagem.visualizar',
                    'empacotamento.criar',
                    'empacotamento.editar',
                    'empacotamento.visualizar',
                    'empacotamento.confirmar_entrega',
                    'motorista.visualizar',
                    'relatorios.visualizar',
                    'relatorios.exportar',
                    'tipos.visualizar',
                    'tipos.criar',
                    'tipos.editar',
                    'tipos.excluir',
                    'status.visualizar',
                    'status.criar',
                    'status.editar',
                    'status.excluir',
                    'qrcodes.visualizar'
                ])
            ]);

        // Transformar Operador (ID 2) em Gestor
        DB::table('niveis_acesso')
            ->where('id', 2)
            ->update([
                'nome' => 'Gestor',
                'descricao' => 'Gerenciamento de operações e visualização de relatórios',
                'permissoes' => json_encode([
                    'estabelecimentos.visualizar',
                    'coletas.criar',
                    'coletas.editar',
                    'coletas.cancelar',
                    'coletas.visualizar',
                    'pesagem.criar',
                    'pesagem.editar',
                    'pesagem.visualizar',
                    'empacotamento.criar',
                    'empacotamento.editar',
                    'empacotamento.visualizar',
                    'empacotamento.confirmar_entrega',
                    'motorista.visualizar',
                    'relatorios.visualizar',
                    'relatorios.exportar',
                    'tipos.visualizar',
                    'status.visualizar',
                    'qrcodes.visualizar'
                ])
            ]);

        // Remover Visualizador (ID 4) - Atualizar usuários para Gestor antes
        DB::table('usuarios')
            ->where('nivel_acesso_id', 4)
            ->update(['nivel_acesso_id' => 2]);

        // Verificar se Visualizador existe antes de deletar
        $visualizador = DB::table('niveis_acesso')->where('id', 4)->where('nome', 'Visualizador')->first();
        if ($visualizador) {
            DB::table('niveis_acesso')->where('id', 4)->delete();
        }

        // Nota: Os níveis Pesagem e Empacotamento serão criados pelo NiveisAcessoSeeder
        // Não precisamos criar aqui para evitar duplicação
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover níveis novos (Pesagem e Empacotamento)
        DB::table('niveis_acesso')->where('nome', 'Pesagem')->delete();
        DB::table('niveis_acesso')->where('nome', 'Empacotamento')->delete();

        // Restaurar Operador
        DB::table('niveis_acesso')
            ->where('id', 2)
            ->update([
                'nome' => 'Operador',
                'descricao' => 'Acesso às operações de coleta, pesagem e empacotamento',
                'permissoes' => json_encode([
                    'estabelecimentos.visualizar',
                    'coletas.criar',
                    'coletas.editar',
                    'coletas.visualizar',
                    'pesagem.criar',
                    'pesagem.editar',
                    'pesagem.visualizar',
                    'empacotamento.criar',
                    'empacotamento.editar',
                    'empacotamento.visualizar',
                    'motorista.visualizar',
                    'relatorios.visualizar'
                ])
            ]);

        // Recriar Visualizador
        DB::table('niveis_acesso')->insert([
            'id' => 4,
            'nome' => 'Visualizador',
            'descricao' => 'Acesso apenas para consulta de relatórios',
            'permissoes' => json_encode([
                'estabelecimentos.visualizar',
                'coletas.visualizar',
                'pesagem.visualizar',
                'empacotamento.visualizar',
                'relatorios.visualizar'
            ]),
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Restaurar permissões antigas do Administrador
        DB::table('niveis_acesso')
            ->where('id', 1)
            ->update([
                'permissoes' => json_encode([
                    'usuarios.criar',
                    'usuarios.editar',
                    'usuarios.excluir',
                    'usuarios.visualizar',
                    'estabelecimentos.criar',
                    'estabelecimentos.editar',
                    'estabelecimentos.excluir',
                    'estabelecimentos.visualizar',
                    'coletas.criar',
                    'coletas.editar',
                    'coletas.cancelar',
                    'coletas.visualizar',
                    'pesagem.criar',
                    'pesagem.editar',
                    'pesagem.visualizar',
                    'empacotamento.criar',
                    'empacotamento.editar',
                    'empacotamento.visualizar',
                    'motorista.visualizar',
                    'relatorios.visualizar',
                    'relatorios.exportar',
                    'tipos.criar',
                    'tipos.editar',
                    'tipos.excluir',
                    'status.criar',
                    'status.editar',
                    'status.excluir'
                ])
            ]);
    }
};

