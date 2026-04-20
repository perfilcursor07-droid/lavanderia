<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = [
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
                'nome' => 'Aguardando empacotamento',
                'descricao' => 'Peças estão aguardando para serem empacotadas',
                'tipo' => 'empacotamento',
                'cor' => '#ffc107',
                'ordem' => 1,
                'ativo' => true
            ],
            [
                'nome' => 'Em empacotamento',
                'descricao' => 'Peças estão sendo empacotadas',
                'tipo' => 'empacotamento',
                'cor' => '#17a2b8',
                'ordem' => 2,
                'ativo' => true
            ],
            [
                'nome' => 'Pronto para motorista',
                'descricao' => 'Empacotamento concluído, aguardando confirmação do motorista para saída',
                'tipo' => 'empacotamento',
                'cor' => '#007bff',
                'ordem' => 3,
                'ativo' => true
            ],
            [
                'nome' => 'Em trânsito',
                'descricao' => 'Empacotamento saiu para entrega',
                'tipo' => 'empacotamento',
                'cor' => '#fd7e14',
                'ordem' => 4,
                'ativo' => true
            ],
            [
                'nome' => 'Entregue',
                'descricao' => 'Empacotamento foi entregue ao cliente',
                'tipo' => 'empacotamento',
                'cor' => '#28a745',
                'ordem' => 5,
                'ativo' => true
            ]
        ];

        foreach ($status as $item) {
            Status::updateOrCreate(
                ['nome' => $item['nome'], 'tipo' => $item['tipo']], // Busca por nome e tipo
                $item // Atualiza ou cria com esses dados
            );
        }
    }
}
