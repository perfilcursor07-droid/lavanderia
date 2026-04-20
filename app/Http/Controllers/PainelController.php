<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coleta;
use App\Models\Empacotamento;
use App\Models\Entrega;
use App\Models\Estabelecimento;
use App\Models\Usuario;
use Carbon\Carbon;

class PainelController extends Controller
{
    /**
     * Exibe a página dedicada para acompanhar coletas
     */
    public function acompanharColetas(Request $request)
    {
        $mesAtual = Carbon::now()->startOfMonth();
        
        // Debug: Ver todas as coletas do mês primeiro
        $todasColetasDoMes = Coleta::with(['estabelecimento', 'status', 'pesagens', 'empacotamento.status', 'empacotamento.entrega.status'])
                                  ->where('created_at', '>=', $mesAtual)
                                  ->orderBy('created_at', 'desc')
                                  ->get();
        
        // Coletas do mês em andamento - versão mais simples e robusta
        $coletasAndamento = collect();
        foreach($todasColetasDoMes as $coleta) {
            $isConcluida = false;
            
            // Verificar se é considerada concluída
            if ($coleta->empacotamento && $coleta->empacotamento->entrega && $coleta->empacotamento->entrega->status) {
                $statusEntrega = $coleta->empacotamento->entrega->status->nome;
                if (in_array($statusEntrega, ['Entregue', 'Confirmado pelo Cliente'])) {
                    $isConcluida = true;
                }
            } elseif ($coleta->empacotamento && $coleta->empacotamento->status) {
                $statusEmp = $coleta->empacotamento->status->nome;
                // Considerar concluída apenas quando realmente entregue
                if ($statusEmp === 'Entregue') {
                    $isConcluida = true;
                }
            }
            
            // Se não é concluída, adicionar às em andamento
            if (!$isConcluida) {
                $coletasAndamento->push($coleta);
            }
        }
        
        // Coletas do mês concluídas - versão mais simples e robusta
        $coletasConcluidas = collect();
        foreach($todasColetasDoMes as $coleta) {
            $isConcluida = false;
            
            // Verificar se é considerada concluída
            if ($coleta->empacotamento && $coleta->empacotamento->entrega && $coleta->empacotamento->entrega->status) {
                $statusEntrega = $coleta->empacotamento->entrega->status->nome;
                if (in_array($statusEntrega, ['Entregue', 'Confirmado pelo Cliente'])) {
                    $isConcluida = true;
                }
            } elseif ($coleta->empacotamento && $coleta->empacotamento->status) {
                $statusEmp = $coleta->empacotamento->status->nome;
                // Considerar concluída apenas quando realmente entregue
                if ($statusEmp === 'Entregue') {
                    $isConcluida = true;
                }
            }
            
            // Se é concluída, adicionar às concluídas
            if ($isConcluida) {
                $coletasConcluidas->push($coleta);
            }
        }
        
        // Estatísticas rápidas usando os dados já processados
        $totalColetas = Coleta::count();
        $totalAndamento = $coletasAndamento->count();
        $totalConcluidas = $coletasConcluidas->count();
        
        // Se for requisição AJAX, retornar JSON para atualização em tempo real
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'timestamp' => now()->format('H:i:s'),
                'totalColetas' => $totalColetas,
                'totalAndamento' => $totalAndamento,
                'totalConcluidas' => $totalConcluidas,
                'coletasAndamento' => $coletasAndamento->map(function($coleta) {
                    // Calcular progresso para cada coleta
                    $statusEmpacotamento = $coleta->empacotamento?->status->nome;
                    $entrega = $coleta->empacotamento?->entrega;
                    $statusEntrega = $entrega?->status->nome;
                    
                    $entregaConcluida = false;
                    if ($statusEmpacotamento && in_array($statusEmpacotamento, ['Em trânsito', 'Entregue'])) {
                        $entregaConcluida = true;
                    } elseif ($entrega && in_array($statusEntrega, ['Em trânsito', 'Entregue', 'Confirmado pelo Cliente'])) {
                        $entregaConcluida = true;
                    }
                    
                    $confirmacaoConcluida = $entrega && in_array($statusEntrega, ['Entregue', 'Confirmado pelo Cliente']);

                    $progresso = [
                        'coleta' => true,
                        'pesagem' => $coleta->pesagens->count() > 0,
                        'empacotamento' => $coleta->empacotamento !== null,
                        'entrega' => $entregaConcluida,
                        'confirmacao_cliente' => $confirmacaoConcluida
                    ];
                    $etapasConcluidas = collect($progresso)->filter()->count();
                    $percentual = round(($etapasConcluidas / 5) * 100);
                    
                    // Calcular tempo total corretamente
                    $tempoTotal = $this->calcularTempoEntre($coleta->created_at, Carbon::now());
                    
                    return [
                        'id' => $coleta->id,
                        'numero_coleta' => $coleta->numero_coleta,
                        'status' => $coleta->status->nome,
                        'status_cor' => $coleta->status->cor,
                        'estabelecimento' => $coleta->estabelecimento->razao_social,
                        'created_at' => $coleta->created_at->format('d/m/Y H:i'),
                        'empacotamento_status' => $statusEmpacotamento,
                        'entrega_status' => $statusEntrega,
                        'progresso' => $progresso,
                        'etapas_concluidas' => $etapasConcluidas,
                        'percentual' => $percentual,
                        'tempo_total' => $tempoTotal,
                    ];
                }),
                'coletasConcluidas' => $coletasConcluidas->map(function($coleta) {
                    // Calcular progresso para cada coleta
                    $statusEmpacotamento = $coleta->empacotamento?->status->nome;
                    $entrega = $coleta->empacotamento?->entrega;
                    $statusEntrega = $entrega?->status->nome;
                    
                    $entregaConcluida = false;
                    if ($statusEmpacotamento && in_array($statusEmpacotamento, ['Em trânsito', 'Entregue'])) {
                        $entregaConcluida = true;
                    } elseif ($entrega && in_array($statusEntrega, ['Em trânsito', 'Entregue', 'Confirmado pelo Cliente'])) {
                        $entregaConcluida = true;
                    }
                    
                    $confirmacaoConcluida = $entrega && in_array($statusEntrega, ['Entregue', 'Confirmado pelo Cliente']);

                    $progresso = [
                        'coleta' => true,
                        'pesagem' => $coleta->pesagens->count() > 0,
                        'empacotamento' => $coleta->empacotamento !== null,
                        'entrega' => $entregaConcluida,
                        'confirmacao_cliente' => $confirmacaoConcluida
                    ];
                    $etapasConcluidas = collect($progresso)->filter()->count();
                    $percentual = round(($etapasConcluidas / 5) * 100);
                    
                    // Calcular tempo total corretamente
                    $dataFinal = $entrega?->data_confirmacao_recebimento ?? $entrega?->data_entrega ?? Carbon::now();
                    $tempoTotal = $this->calcularTempoEntre($coleta->created_at, $dataFinal);
                    
                    return [
                        'id' => $coleta->id,
                        'numero_coleta' => $coleta->numero_coleta,
                        'status' => $coleta->status->nome,
                        'status_cor' => $coleta->status->cor,
                        'estabelecimento' => $coleta->estabelecimento->razao_social,
                        'created_at' => $coleta->created_at->format('d/m/Y H:i'),
                        'empacotamento_status' => $statusEmpacotamento,
                        'entrega_status' => $statusEntrega,
                        'progresso' => $progresso,
                        'etapas_concluidas' => $etapasConcluidas,
                        'percentual' => $percentual,
                        'tempo_total' => $tempoTotal,
                    ];
                }),
            ]);
        }
        
        return view('acompanhar-coletas.index', compact(
            'coletasAndamento',
            'coletasConcluidas',
            'totalColetas',
            'totalAndamento', 
            'totalConcluidas'
        ));
    }

    /**
     * Exibe o dashboard principal
     */
    public function index()
    {
        $hoje = Carbon::today();
        $mesAtual = Carbon::now()->startOfMonth();
        
        // Estatísticas do dia
        $coletasHoje = Coleta::whereDate('created_at', $hoje)->count();
        $empacotamentosHoje = Empacotamento::whereDate('created_at', $hoje)->count();
        $pesoTotalHoje = Coleta::whereDate('created_at', $hoje)->sum('peso_total');
        $pesagensHoje = \App\Models\Pesagem::whereDate('created_at', $hoje)->count();

        // Estatísticas do mês
        $coletasMes = Coleta::where('created_at', '>=', $mesAtual)->count();
        $empacotamentosMes = Empacotamento::where('created_at', '>=', $mesAtual)->count();
        $pesoTotalMes = Coleta::where('created_at', '>=', $mesAtual)->sum('peso_total');
        $pesagensMes = \App\Models\Pesagem::where('created_at', '>=', $mesAtual)->count();
        
        // Coletas recentes
        $coletasRecentes = Coleta::with(['estabelecimento', 'status'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        // Coletas para acompanhamento (últimas 20)
        $coletasAcompanhamento = Coleta::with(['estabelecimento', 'status', 'pesagens', 'empacotamento.status', 'empacotamento.entrega.status'])
                                      ->orderBy('created_at', 'desc')
                                      ->limit(20)
                                      ->get();
        
        // Empacotamentos pendentes
        $empacotamentosPendentes = Empacotamento::with(['coleta.estabelecimento', 'status'])
                                               ->whereHas('status', function($query) {
                                                   $query->whereIn('nome', ['Aguardando empacotamento', 'Em empacotamento', 'Pronto para entrega']);
                                               })
                                               ->orderBy('created_at', 'asc')
                                               ->limit(5)
                                               ->get();
        
        // Estatísticas gerais
        $totalEstabelecimentos = Estabelecimento::where('ativo', true)->count();
        $totalUsuarios = Usuario::where('ativo', true)->count();
        
        return view('painel.index', compact(
            'coletasHoje',
            'empacotamentosHoje',
            'pesoTotalHoje',
            'pesagensHoje',
            'coletasMes',
            'empacotamentosMes',
            'pesoTotalMes',
            'pesagensMes',
            'coletasRecentes',
            'empacotamentosPendentes',
            'coletasAcompanhamento',
            'totalEstabelecimentos',
            'totalUsuarios'
        ));
    }

    /**
     * Calcular progresso de uma coleta com tempos entre etapas
     */
    private function calcularProgressoColeta($coleta)
    {
        // Datas das etapas
        $dataColeta = $coleta->created_at;
        $dataPesagem = $coleta->pesagens->first()?->created_at;
        $dataEmpacotamento = $coleta->empacotamento?->data_empacotamento;
        $entrega = $coleta->empacotamento?->entrega;
        $dataEntrega = $entrega?->data_entrega;

        $progresso = [
            'coleta' => [
                'concluida' => true,
                'data' => $dataColeta,
                'status' => $coleta->status->nome,
                'tempo_desde_inicio' => '0h 0m'
            ],
            'pesagem' => [
                'concluida' => $coleta->pesagens->count() > 0,
                'data' => $dataPesagem,
                'quantidade' => $coleta->pesagens->count(),
                'tempo_desde_coleta' => $dataPesagem ? $this->calcularTempoEntre($dataColeta, $dataPesagem) : null,
                'tempo_desde_inicio' => $dataPesagem ? $this->calcularTempoEntre($dataColeta, $dataPesagem) : null
            ],
            'empacotamento' => [
                'concluida' => $coleta->empacotamento !== null,
                'data' => $dataEmpacotamento,
                'status' => $coleta->empacotamento?->status->nome,
                'codigo_qr' => $coleta->empacotamento?->codigo_qr,
                'tempo_desde_pesagem' => $dataEmpacotamento && $dataPesagem ? $this->calcularTempoEntre($dataPesagem, $dataEmpacotamento) : null,
                'tempo_desde_inicio' => $dataEmpacotamento ? $this->calcularTempoEntre($dataColeta, $dataEmpacotamento) : null
            ],
            'entrega' => [
                'concluida' => $entrega && in_array($entrega->status->nome, ['Em trânsito', 'Entregue', 'Confirmado pelo Cliente']),
                'data' => $entrega?->data_saida,
                'motorista' => $entrega?->motoristaSaida?->nome,
                'tempo_desde_empacotamento' => $entrega && $entrega->data_saida && $dataEmpacotamento ? $this->calcularTempoEntre($dataEmpacotamento, $entrega->data_saida) : null,
                'tempo_desde_inicio' => $entrega && $entrega->data_saida ? $this->calcularTempoEntre($dataColeta, $entrega->data_saida) : null
            ],
            'confirmacao_cliente' => [
                'concluida' => $entrega && in_array($entrega->status->nome, ['Entregue', 'Confirmado pelo Cliente']),
                'data' => $dataEntrega,
                'nome_recebedor' => $entrega?->nome_recebedor,
                'assinatura' => $entrega?->assinatura_recebedor,
                'tempo_desde_entrega' => $dataEntrega && $entrega && $entrega->data_saida ?
                    $this->calcularTempoEntre($entrega->data_saida, $dataEntrega) : null,
                'tempo_desde_inicio' => $dataEntrega ?
                    $this->calcularTempoEntre($dataColeta, $dataEntrega) : null
            ]
        ];

        // Calcular percentual (5 etapas agora)
        $etapasConcluidas = 0;
        if ($progresso['coleta']['concluida']) $etapasConcluidas++;
        if ($progresso['pesagem']['concluida']) $etapasConcluidas++;
        if ($progresso['empacotamento']['concluida']) $etapasConcluidas++;
        if ($progresso['entrega']['concluida']) $etapasConcluidas++;
        if ($progresso['confirmacao_cliente']['concluida']) $etapasConcluidas++;

        $progresso['percentual'] = round(($etapasConcluidas / 5) * 100);

        // Tempo total do processo (se confirmado pelo cliente)
        if ($progresso['confirmacao_cliente']['concluida']) {
            $progresso['tempo_total'] = $this->calcularTempoEntre($dataColeta, $entrega->data_confirmacao_recebimento);
        } elseif ($progresso['entrega']['concluida']) {
            $progresso['tempo_total'] = $this->calcularTempoEntre($dataColeta, $dataEntrega);
        } else {
            // Tempo até agora
            $progresso['tempo_total'] = $this->calcularTempoEntre($dataColeta, Carbon::now());
        }

        return $progresso;
    }

    /**
     * Calcular tempo entre duas datas
     */
    private function calcularTempoEntre($dataInicio, $dataFim)
    {
        if (!$dataInicio || !$dataFim) {
            return null;
        }

        $inicio = Carbon::parse($dataInicio);
        $fim = Carbon::parse($dataFim);

        $diffInMinutes = $inicio->diffInMinutes($fim);

        if ($diffInMinutes < 60) {
            return $diffInMinutes . 'm';
        } elseif ($diffInMinutes < 1440) { // menos de 24 horas
            $horas = floor($diffInMinutes / 60);
            $minutos = $diffInMinutes % 60;
            return $horas . 'h ' . $minutos . 'm';
        } else { // mais de 24 horas
            $dias = floor($diffInMinutes / 1440);
            $horasRestantes = floor(($diffInMinutes % 1440) / 60);
            return $dias . 'd ' . $horasRestantes . 'h';
        }
    }

    /**
     * Acompanhamento de coleta
     */
    public function acompanharColeta(Request $request)
    {
        $request->validate([
            'numero_coleta' => 'required|string'
        ]);

        $coleta = Coleta::with(['estabelecimento', 'status', 'pesagens', 'empacotamento.status', 'empacotamento.entrega.status'])
            ->where('numero_coleta', $request->numero_coleta)
            ->first();

        if (!$coleta) {
            return response()->json([
                'success' => false,
                'message' => 'Coleta não encontrada'
            ]);
        }

        // Calcular progresso da coleta
        $progresso = $this->calcularProgressoColeta($coleta);

        return response()->json([
            'success' => true,
            'coleta' => $coleta,
            'progresso' => $progresso
        ]);
    }
}
