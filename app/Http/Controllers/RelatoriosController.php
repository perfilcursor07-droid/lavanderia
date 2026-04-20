<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coleta;
use App\Models\Empacotamento;
use App\Models\Entrega;
use App\Models\Estabelecimento;
use App\Models\Usuario;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatoriosController extends Controller
{
    /**
     * Página principal de relatórios
     */
    public function index(Request $request)
    {
        // Filtros com valores padrão
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));
        $estabelecimentoId = $request->get('estabelecimento_id', '');
        $motoristaId = $request->get('motorista_id', '');
        $statusId = $request->get('status_id', '');
        $tipoRelatorio = $request->get('tipo_relatorio', 'coletas');

        // Dados para filtros
        $estabelecimentos = Estabelecimento::orderBy('razao_social')->get();
        $motoristas = Usuario::motoristas()->orderBy('nome')->get();
        $statuses = Status::orderBy('ordem')->get();

        // Gerar relatório baseado no tipo
        $dados = $this->gerarRelatorio($tipoRelatorio, $dataInicio, $dataFim, $estabelecimentoId, $motoristaId, $statusId);

        // Garantir que todas as variáveis estão definidas
        return view('relatorios.index', [
            'dados' => $dados,
            'estabelecimentos' => $estabelecimentos,
            'motoristas' => $motoristas,
            'statuses' => $statuses,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
            'estabelecimentoId' => $estabelecimentoId,
            'motoristaId' => $motoristaId,
            'statusId' => $statusId,
            'tipoRelatorio' => $tipoRelatorio
        ]);
    }

    /**
     * Gerar relatório baseado no tipo
     */
    private function gerarRelatorio($tipo, $dataInicio, $dataFim, $estabelecimentoId, $motoristaId, $statusId)
    {
        switch ($tipo) {
            case 'coletas':
                return $this->relatorioColetas($dataInicio, $dataFim, $estabelecimentoId, $statusId);
            case 'entregas':
                return $this->relatorioEntregas($dataInicio, $dataFim, $motoristaId, $statusId);
            case 'financeiro':
                return $this->relatorioFinanceiro($dataInicio, $dataFim, $estabelecimentoId);
            case 'performance':
                return $this->relatorioPerformance($dataInicio, $dataFim, $motoristaId);
            default:
                return $this->relatorioColetas($dataInicio, $dataFim, $estabelecimentoId, $statusId);
        }
    }

    /**
     * Relatório de Coletas
     */
    private function relatorioColetas($dataInicio, $dataFim, $estabelecimentoId, $statusId)
    {
        $query = Coleta::with(['estabelecimento', 'status', 'pesagens', 'empacotamento.entrega'])
            ->whereBetween('created_at', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);

        if ($estabelecimentoId) {
            $query->where('estabelecimento_id', $estabelecimentoId);
        }

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $coletas = $query->orderBy('created_at', 'desc')->get();

        // Estatísticas
        $totalColetas = $coletas->count();
        $pesoTotal = $coletas->sum('peso_total');
        $coletasPorStatus = $coletas->groupBy('status.nome')->map->count();
        $coletasPorEstabelecimento = $coletas->groupBy('estabelecimento.razao_social')->map->count();

        return [
            'tipo' => 'coletas',
            'coletas' => $coletas,
            'estatisticas' => [
                'total_coletas' => $totalColetas,
                'peso_total' => $pesoTotal,
                'coletas_por_status' => $coletasPorStatus,
                'coletas_por_estabelecimento' => $coletasPorEstabelecimento
            ]
        ];
    }

    /**
     * Relatório de Entregas
     */
    private function relatorioEntregas($dataInicio, $dataFim, $motoristaId, $statusId)
    {
        $query = Entrega::with(['empacotamento.coleta.estabelecimento', 'motoristaSaida', 'motoristaEntrega', 'status'])
            ->whereBetween('created_at', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);

        if ($motoristaId) {
            $query->where(function($q) use ($motoristaId) {
                $q->where('motorista_saida_id', $motoristaId)
                  ->orWhere('motorista_entrega_id', $motoristaId);
            });
        }

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $entregas = $query->orderBy('created_at', 'desc')->get();

        // Estatísticas
        $totalEntregas = $entregas->count();
        $entregasConfirmadas = $entregas->where('status.nome', 'Confirmado pelo Cliente')->count();
        $tempoMedioEntrega = $this->calcularTempoMedioEntrega($entregas);
        $entregasPorMotorista = $entregas->groupBy('motoristaEntrega.nome')->map->count();

        return [
            'tipo' => 'entregas',
            'entregas' => $entregas,
            'estatisticas' => [
                'total_entregas' => $totalEntregas,
                'entregas_confirmadas' => $entregasConfirmadas,
                'tempo_medio_entrega' => $tempoMedioEntrega,
                'entregas_por_motorista' => $entregasPorMotorista
            ]
        ];
    }

    /**
     * Relatório Financeiro
     */
    private function relatorioFinanceiro($dataInicio, $dataFim, $estabelecimentoId)
    {
        $query = Coleta::with(['estabelecimento', 'empacotamento.entrega'])
            ->whereBetween('created_at', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);

        if ($estabelecimentoId) {
            $query->where('estabelecimento_id', $estabelecimentoId);
        }

        $coletas = $query->get();

        // Cálculos financeiros (exemplo - adapte conforme sua regra de negócio)
        $receitaTotal = $coletas->sum('peso_total') * 5; // R$ 5 por kg (exemplo)
        $coletasFinalizadas = $coletas->filter(function($coleta) {
            return $coleta->empacotamento && $coleta->empacotamento->entrega &&
                   $coleta->empacotamento->entrega->status->nome === 'Confirmado pelo Cliente';
        });
        $receitaConfirmada = $coletasFinalizadas->sum('peso_total') * 5;

        return [
            'tipo' => 'financeiro',
            'coletas' => $coletas,
            'estatisticas' => [
                'receita_total' => $receitaTotal,
                'receita_confirmada' => $receitaConfirmada,
                'coletas_finalizadas' => $coletasFinalizadas->count(),
                'peso_total' => $coletas->sum('peso_total')
            ]
        ];
    }

    /**
     * Relatório de Performance
     */
    private function relatorioPerformance($dataInicio, $dataFim, $motoristaId)
    {
        $query = Entrega::with(['empacotamento.coleta', 'motoristaSaida', 'motoristaEntrega'])
            ->whereBetween('created_at', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);

        if ($motoristaId) {
            $query->where('motorista_entrega_id', $motoristaId);
        }

        $entregas = $query->get();

        // Métricas de performance
        $tempoMedioEntrega = $this->calcularTempoMedioEntrega($entregas);
        $taxaConfirmacao = $entregas->count() > 0 ?
            ($entregas->where('status.nome', 'Confirmado pelo Cliente')->count() / $entregas->count()) * 100 : 0;

        return [
            'tipo' => 'performance',
            'entregas' => $entregas,
            'estatisticas' => [
                'tempo_medio_entrega' => $tempoMedioEntrega,
                'taxa_confirmacao' => round($taxaConfirmacao, 2),
                'total_entregas' => $entregas->count(),
                'entregas_confirmadas' => $entregas->where('status.nome', 'Confirmado pelo Cliente')->count()
            ]
        ];
    }

    /**
     * Calcular tempo médio de entrega
     */
    private function calcularTempoMedioEntrega($entregas)
    {
        $temposEntrega = [];

        foreach ($entregas as $entrega) {
            if ($entrega->data_saida && $entrega->data_entrega) {
                $temposEntrega[] = Carbon::parse($entrega->data_saida)->diffInMinutes($entrega->data_entrega);
            }
        }

        if (empty($temposEntrega)) {
            return 'N/A';
        }

        $tempoMedio = array_sum($temposEntrega) / count($temposEntrega);

        if ($tempoMedio < 60) {
            return round($tempoMedio) . ' min';
        } else {
            $horas = floor($tempoMedio / 60);
            $minutos = round($tempoMedio % 60);
            return $horas . 'h ' . $minutos . 'm';
        }
    }

    /**
     * Exportar relatório para Excel/PDF
     */
    public function exportar(Request $request)
    {
        // Implementar exportação se necessário
        return response()->json(['message' => 'Funcionalidade de exportação em desenvolvimento']);
    }
}
