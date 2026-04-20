<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empacotamento;

class RastreamentoController extends Controller
{
    /**
     * Página de rastreamento público
     */
    public function index($codigo)
    {
        // Buscar empacotamento com todos os relacionamentos necessários
        $empacotamento = Empacotamento::where('codigo_qr', $codigo)
            ->with([
                'coleta' => function($query) {
                    $query->with([
                        'estabelecimento',
                        'status',
                        'usuario',
                        'pecas.tipo',
                        'pesagens'
                    ]);
                },
                'status',
                'usuarioEmpacotamento',
                'motorista',
                'motoristaSaida',
                'motoristaEntrega',
                'entrega.status'
            ])
            ->first();

        if (!$empacotamento) {
            return view('rastreamento.nao-encontrado', compact('codigo'));
        }

        // Calcular timeline do pedido
        $timeline = $this->calcularTimeline($empacotamento);

        return view('rastreamento.index', compact('empacotamento', 'timeline', 'codigo'));
    }

    /**
     * Calcular timeline do pedido
     */
    private function calcularTimeline($empacotamento)
    {
        $timeline = collect();
        $coleta = $empacotamento->coleta;

        // 1. Coleta agendada
        if ($coleta->data_agendamento) {
            $timeline->push([
                'titulo' => 'Coleta Agendada',
                'descricao' => 'Coleta foi agendada para ' . $coleta->data_agendamento->format('d/m/Y'),
                'data' => $coleta->data_agendamento,
                'icone' => 'calendar',
                'status' => 'concluido'
            ]);
        }

        // 2. Coleta realizada
        if ($coleta->data_coleta) {
            $timeline->push([
                'titulo' => 'Coleta Realizada',
                'descricao' => 'Roupas coletadas em ' . $coleta->data_coleta->format('d/m/Y H:i'),
                'data' => $coleta->data_coleta,
                'icone' => 'truck',
                'status' => 'concluido'
            ]);
        }

        // 3. Pesagens (se existirem)
        if ($coleta->pesagens->count() > 0) {
            $pesagemMaisRecente = $coleta->pesagens->sortByDesc('created_at')->first();
            $timeline->push([
                'titulo' => 'Pesagem Realizada',
                'descricao' => 'Peso total: ' . number_format($coleta->pesagens->sum('peso'), 2, ',', '.') . ' kg',
                'data' => $pesagemMaisRecente->created_at,
                'icone' => 'scale',
                'status' => 'concluido'
            ]);
        }

        // 4. Empacotamento
        if ($empacotamento->data_empacotamento) {
            $timeline->push([
                'titulo' => 'Empacotamento Concluído',
                'descricao' => 'Roupas limpas e embaladas em ' . $empacotamento->data_empacotamento->format('d/m/Y H:i'),
                'data' => $empacotamento->data_empacotamento,
                'icone' => 'package',
                'status' => 'concluido'
            ]);
        }

        // 5. Saída para entrega
        if ($empacotamento->data_saida) {
            $timeline->push([
                'titulo' => 'Saiu para Entrega',
                'descricao' => 'Pedido saiu para entrega em ' . $empacotamento->data_saida->format('d/m/Y H:i'),
                'data' => $empacotamento->data_saida,
                'icone' => 'truck-delivery',
                'status' => 'concluido'
            ]);
        }

        // 6. Entregue
        if ($empacotamento->data_entrega) {
            $timeline->push([
                'titulo' => 'Entregue',
                'descricao' => 'Pedido entregue em ' . $empacotamento->data_entrega->format('d/m/Y H:i') . 
                              ($empacotamento->nome_recebedor ? ' para ' . $empacotamento->nome_recebedor : ''),
                'data' => $empacotamento->data_entrega,
                'icone' => 'check-circle',
                'status' => 'concluido'
            ]);
        }

        // 7. Confirmado pelo cliente
        if ($empacotamento->data_confirmacao_recebimento) {
            $timeline->push([
                'titulo' => 'Confirmado pelo Cliente',
                'descricao' => 'Recebimento confirmado pelo cliente em ' . $empacotamento->data_confirmacao_recebimento->format('d/m/Y H:i'),
                'data' => $empacotamento->data_confirmacao_recebimento,
                'icone' => 'user-check',
                'status' => 'concluido'
            ]);
        }

        // Status atual se não estiver finalizado
        $statusAtual = $empacotamento->status->nome;
        if (!in_array($statusAtual, ['Entregue', 'Confirmado pelo Cliente'])) {
            $timeline->push([
                'titulo' => $statusAtual,
                'descricao' => 'Status atual do pedido',
                'data' => now(),
                'icone' => 'info-circle',
                'status' => 'atual'
            ]);
        }

        return $timeline->sortBy('data');
    }
}

