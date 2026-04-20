<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NivelAcesso;

class NiveisAcessoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveis = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo a todas as funcionalidades do sistema',
                'permissoes' => [
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
                    'tipos.visualizar',
                    'tipos.criar',
                    'tipos.editar',
                    'tipos.excluir',
                    'status.criar',
                    'status.editar',
                    'status.excluir'
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Operador',
                'descricao' => 'Acesso às operações de coleta, pesagem e empacotamento',
                'permissoes' => [
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
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Motorista',
                'descricao' => 'Acesso específico para confirmação de entregas',
                'permissoes' => [
                    'coletas.visualizar',
                    'coletas.criar',
                    'coletas.editar',
                    'empacotamento.visualizar',
                    'empacotamento.confirmar_entrega',
                    'motorista.visualizar',
                    'qrcodes.visualizar'
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Visualizador',
                'descricao' => 'Acesso apenas para consulta de relatórios',
                'permissoes' => [
                    'estabelecimentos.visualizar',
                    'coletas.visualizar',
                    'pesagem.visualizar',
                    'empacotamento.visualizar',
                    'relatorios.visualizar'
                ],
                'ativo' => true
            ]
        ];

        foreach ($niveis as $nivel) {
            NivelAcesso::updateOrCreate(
                ['nome' => $nivel['nome']],
                $nivel
            );
        }
    }
}
