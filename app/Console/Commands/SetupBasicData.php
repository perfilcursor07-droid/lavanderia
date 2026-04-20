<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Status;
use App\Models\Tipo;
use App\Models\NivelAcesso;

class SetupBasicData extends Command
{
    protected $signature = 'setup:basic-data';
    protected $description = 'Configurar dados básicos do sistema (Status, Tipos, Níveis de Acesso)';

    public function handle()
    {
        $this->info('🚀 Configurando dados básicos do sistema...');

        $this->createStatus();
        $this->createTipos();
        $this->createNiveisAcesso();

        $this->info('✅ Dados básicos configurados com sucesso!');
        return 0;
    }

    private function createStatus()
    {
        $this->info('📊 Criando status...');

        $statusData = [
            // Status para coletas
            [
                'nome' => 'Agendada',
                'descricao' => 'Coleta foi agendada mas ainda não foi realizada',
                'tipo' => 'coleta',
                'cor' => '#ffc107',
                'ordem' => 1,
                'ativo' => true
            ],
            [
                'nome' => 'Em andamento',
                'descricao' => 'Coleta está sendo realizada',
                'tipo' => 'coleta',
                'cor' => '#17a2b8',
                'ordem' => 2,
                'ativo' => true
            ],
            [
                'nome' => 'Concluída',
                'descricao' => 'Coleta foi concluída com sucesso',
                'tipo' => 'coleta',
                'cor' => '#28a745',
                'ordem' => 3,
                'ativo' => true
            ],
            [
                'nome' => 'Cancelada',
                'descricao' => 'Coleta foi cancelada',
                'tipo' => 'coleta',
                'cor' => '#dc3545',
                'ordem' => 4,
                'ativo' => true
            ],
            // Status para empacotamento
            [
                'nome' => 'Aguardando',
                'descricao' => 'Aguardando início do empacotamento',
                'tipo' => 'empacotamento',
                'cor' => '#6c757d',
                'ordem' => 1,
                'ativo' => true
            ],
            [
                'nome' => 'Em Processamento',
                'descricao' => 'Empacotamento em andamento',
                'tipo' => 'empacotamento',
                'cor' => '#fd7e14',
                'ordem' => 2,
                'ativo' => true
            ],
            [
                'nome' => 'Pronto para motorista',
                'descricao' => 'Empacotamento concluído, aguardando confirmação do motorista para saída',
                'tipo' => 'empacotamento',
                'cor' => '#20c997',
                'ordem' => 3,
                'ativo' => true
            ],
            [
                'nome' => 'Em Trânsito',
                'descricao' => 'Saiu para entrega',
                'tipo' => 'empacotamento',
                'cor' => '#0dcaf0',
                'ordem' => 4,
                'ativo' => true
            ],
            [
                'nome' => 'Entregue',
                'descricao' => 'Entrega realizada com sucesso',
                'tipo' => 'empacotamento',
                'cor' => '#198754',
                'ordem' => 5,
                'ativo' => true
            ]
        ];

        foreach ($statusData as $status) {
            Status::firstOrCreate(
                ['nome' => $status['nome'], 'tipo' => $status['tipo']],
                $status
            );
            $this->line("  ✓ Status: {$status['nome']} ({$status['tipo']})");
        }
    }

    private function createTipos()
    {
        $this->info('🏷️ Criando tipos...');

        $tipos = [
            ['nome' => 'Lençol Solteiro', 'descricao' => 'Lençol para cama de solteiro', 'categoria' => 'roupa_cama'],
            ['nome' => 'Lençol Casal', 'descricao' => 'Lençol para cama de casal', 'categoria' => 'roupa_cama'],
            ['nome' => 'Fronha', 'descricao' => 'Fronha para travesseiro', 'categoria' => 'roupa_cama'],
            ['nome' => 'Edredom', 'descricao' => 'Edredom/cobertor', 'categoria' => 'roupa_cama'],
            ['nome' => 'Toalha de Banho', 'descricao' => 'Toalha de banho grande', 'categoria' => 'roupa_banho'],
            ['nome' => 'Toalha de Rosto', 'descricao' => 'Toalha de rosto pequena', 'categoria' => 'roupa_banho'],
            ['nome' => 'Camisa', 'descricao' => 'Camisa social ou casual', 'categoria' => 'vestuario'],
            ['nome' => 'Calça', 'descricao' => 'Calça social ou casual', 'categoria' => 'vestuario'],
            ['nome' => 'Peso', 'descricao' => 'Tipo especial para coletas realizadas por peso (kg)', 'categoria' => 'peso'],
        ];

        foreach ($tipos as $tipo) {
            Tipo::firstOrCreate(
                ['nome' => $tipo['nome']],
                array_merge($tipo, ['ativo' => true])
            );
            $this->line("  ✓ Tipo: {$tipo['nome']}");
        }
    }

    private function createNiveisAcesso()
    {
        $this->info('👥 Criando níveis de acesso...');

        $niveis = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo a todas as funcionalidades do sistema',
                'permissoes' => [
                    'usuarios.criar', 'usuarios.editar', 'usuarios.excluir', 'usuarios.visualizar',
                    'estabelecimentos.criar', 'estabelecimentos.editar', 'estabelecimentos.excluir', 'estabelecimentos.visualizar',
                    'coletas.criar', 'coletas.editar', 'coletas.cancelar', 'coletas.visualizar',
                    'pesagem.criar', 'pesagem.editar', 'pesagem.visualizar',
                    'empacotamento.criar', 'empacotamento.editar', 'empacotamento.visualizar',
                    'relatorios.visualizar', 'relatorios.exportar'
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Operador',
                'descricao' => 'Acesso às operações principais do sistema',
                'permissoes' => [
                    'coletas.criar', 'coletas.editar', 'coletas.visualizar',
                    'pesagem.criar', 'pesagem.editar', 'pesagem.visualizar',
                    'empacotamento.criar', 'empacotamento.editar', 'empacotamento.visualizar',
                    'estabelecimentos.visualizar'
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Motorista',
                'descricao' => 'Acesso limitado para motoristas',
                'permissoes' => [
                    'coletas.visualizar',
                    'empacotamento.visualizar'
                ],
                'ativo' => true
            ]
        ];

        foreach ($niveis as $nivel) {
            NivelAcesso::firstOrCreate(
                ['nome' => $nivel['nome']],
                $nivel
            );
            $this->line("  ✓ Nível: {$nivel['nome']}");
        }
    }
}
