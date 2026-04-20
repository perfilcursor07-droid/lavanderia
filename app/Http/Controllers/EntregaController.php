<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empacotamento;
use App\Models\EmpacotamentoPeca;
use App\Models\Entrega;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    /**
     * Dashboard do motorista
     */
    public function dashboard()
    {
        $motorista = Auth::user();

        // Peças prontas para retirada (apenas peças que não saíram ainda)
        $pecasProntas = EmpacotamentoPeca::with([
            'empacotamento.coleta.estabelecimento', 
            'empacotamento.status',
            'tipo'
        ])
        ->whereHas('empacotamento.status', function($query) {
            $query->where('nome', 'Pronto para motorista');
        })
        ->where(function($query) {
            $query->whereNull('status_saida')
                  ->orWhere('status_saida', 'pronto');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // Peças em trânsito (retiradas por este motorista mas ainda não entregues)
        $pecasEmTransito = EmpacotamentoPeca::with([
            'empacotamento.coleta.estabelecimento',
            'tipo',
            'motoristaSaida'
        ])
        ->where('motorista_saida_id', $motorista->id)
        ->where('status_saida', 'em_transito')
        ->whereNull('data_entrega')
        ->orderBy('data_saida', 'desc')
        ->get();

        // Peças entregues hoje (por este motorista)
        $pecasEntreguesHoje = EmpacotamentoPeca::with([
            'empacotamento.coleta.estabelecimento',
            'tipo',
            'motoristaEntrega'
        ])
        ->where('motorista_entrega_id', $motorista->id)
        ->whereDate('data_entrega', today())
        ->orderBy('data_entrega', 'desc')
        ->get();

        // Empacotamentos para compatibilidade com views existentes
        $empacotamentosProntos = Empacotamento::with(['coleta.estabelecimento', 'status', 'entrega'])
            ->whereHas('status', function($query) {
                $query->where('nome', 'Pronto para motorista');
            })
            ->orderBy('data_empacotamento', 'desc')
            ->get();

        $empacotamentosEmTransito = Empacotamento::with(['coleta.estabelecimento', 'status', 'entrega'])
            ->whereHas('entrega', function($query) use ($motorista) {
                $query->where('motorista_saida_id', $motorista->id)
                      ->whereHas('status', function($subQuery) {
                          $subQuery->where('nome', 'Em trânsito');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $empacotamentosEntregues = Empacotamento::with(['coleta.estabelecimento', 'status', 'entrega.motoristaEntrega'])
            ->whereHas('entrega', function($query) use ($motorista) {
                $query->where('motorista_entrega_id', $motorista->id)
                      ->whereHas('status', function($subQuery) {
                          $subQuery->whereIn('nome', ['Entregue', 'Confirmado pelo Cliente']);
                      })
                      ->whereDate('data_entrega', today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Estatísticas
        $totalPecasProntas = $pecasProntas->count();
        $totalPecasEmTransito = $pecasEmTransito->count();
        $totalPecasEntreguesHoje = $pecasEntreguesHoje->count();
        $totalProntos = $empacotamentosProntos->count();
        $totalEmTransito = $empacotamentosEmTransito->count();
        $totalEntreguesHoje = $empacotamentosEntregues->count();
        $totalEntreguesMotorista = EmpacotamentoPeca::where('motorista_entrega_id', $motorista->id)->count();

        return view('motorista.dashboard', compact(
            'empacotamentosProntos',
            'empacotamentosEmTransito', 
            'empacotamentosEntregues',
            'pecasProntas',
            'pecasEmTransito',
            'pecasEntreguesHoje',
            'totalProntos',
            'totalEmTransito',
            'totalEntreguesHoje',
            'totalEntreguesMotorista',
            'totalPecasProntas',
            'totalPecasEmTransito',
            'totalPecasEntreguesHoje'
        ));
    }

    /**
     * Buscar peça por QR Code ou código
     */
    public function buscarPeca(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);

        $peca = EmpacotamentoPeca::with([
            'empacotamento.coleta.estabelecimento', 
            'empacotamento.status',
            'tipo',
            'motoristaSaida',
            'motoristaEntrega'
        ])
        ->where('codigo_qr', $request->codigo)
        ->first();

        if (!$peca) {
            return response()->json([
                'success' => false,
                'message' => 'Peça não encontrada'
            ]);
        }

        return response()->json([
            'success' => true,
            'peca' => $peca
        ]);
    }

    /**
     * Buscar empacotamento por QR Code ou código (mantido para compatibilidade)
     */
    public function buscarEmpacotamento(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);

        $empacotamento = Empacotamento::with(['coleta.estabelecimento', 'coleta.pecas.tipo', 'status', 'entrega'])
            ->where('codigo_qr', $request->codigo)
            ->first();

        if (!$empacotamento) {
            return response()->json([
                'success' => false,
                'message' => 'Empacotamento não encontrado'
            ]);
        }

        return response()->json([
            'success' => true,
            'empacotamento' => $empacotamento
        ]);
    }

    /**
     * Confirmar saída de uma peça individual para entrega
     */
    public function confirmarSaidaPeca(Request $request)
    {
        $request->validate([
            'peca_id' => 'required|exists:empacotamento_pecas,id'
        ]);

        DB::beginTransaction();
        try {
            $peca = EmpacotamentoPeca::with(['empacotamento.status'])->findOrFail($request->peca_id);

            // Verificar se o empacotamento está pronto para entrega
            if ($peca->empacotamento->status->nome !== 'Pronto para motorista') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empacotamento não está pronto para entrega'
                ]);
            }

            // Verificar se a peça já saiu
            if ($peca->status_saida === 'em_transito' || $peca->motorista_saida_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta peça já foi retirada por outro motorista'
                ]);
            }

            // Atualizar a peça
            $peca->update([
                'status_saida' => 'em_transito',
                'data_saida' => now(),
                'motorista_saida_id' => Auth::id()
            ]);

            // Verificar se todas as peças do empacotamento saíram
            $pecasRestantes = EmpacotamentoPeca::where('empacotamento_id', $peca->empacotamento_id)
                ->where(function($query) {
                    $query->where('status_saida', '!=', 'em_transito')
                          ->orWhereNull('status_saida')
                          ->orWhere('status_saida', 'pronto');
                })
                ->count();

            // Se todas as peças saíram, atualizar o status do empacotamento
            if ($pecasRestantes == 0) {
                $statusEmTransito = Status::where('nome', 'Em trânsito')->first();
                if ($statusEmTransito) {
                    $peca->empacotamento->update(['status_id' => $statusEmTransito->id]);
                    
                    // Atualizar ou criar registro de entrega para o empacotamento
                    Entrega::updateOrCreate(
                        ['empacotamento_id' => $peca->empacotamento_id],
                        [
                            'status_id' => $statusEmTransito->id,
                            'data_saida' => now(),
                            'motorista_saida_id' => Auth::id()
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saída da peça confirmada com sucesso!',
                'peca' => $peca,
                'todas_pecas_sairam' => $pecasRestantes == 0
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar saída: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirmar saída de todas as peças do empacotamento
     */
    public function confirmarSaida(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id'
        ]);

        DB::beginTransaction();
        try {
            $empacotamento = Empacotamento::findOrFail($request->empacotamento_id);

            // Verificar se está pronto para entrega
            if ($empacotamento->status->nome !== 'Pronto para motorista') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empacotamento não está pronto para entrega'
                ]);
            }

            // Verificar se já tem entrega em andamento
            $entregaExistente = $empacotamento->entrega;
            if ($entregaExistente && $entregaExistente->motorista_saida_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empacotamento já foi assumido por outro motorista'
                ]);
            }

            // Buscar status "Em trânsito"
            $statusEmTransito = Status::where('nome', 'Em trânsito')->first();
            if (!$statusEmTransito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status "Em trânsito" não encontrado'
                ]);
            }

            // Atualizar todas as peças do empacotamento
            EmpacotamentoPeca::where('empacotamento_id', $empacotamento->id)
                ->where(function($query) {
                    $query->whereNull('status_saida')
                          ->orWhere('status_saida', 'pronto');
                })
                ->update([
                    'status_saida' => 'em_transito',
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]);

            // Atualizar empacotamento
            $empacotamento->update(['status_id' => $statusEmTransito->id]);

            // Criar ou atualizar entrega
            Entrega::updateOrCreate(
                ['empacotamento_id' => $empacotamento->id],
                [
                    'status_id' => $statusEmTransito->id,
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saída de todas as peças confirmada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar saída: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirmar entrega de uma peça individual
     */
    public function confirmarEntregaPeca(Request $request)
    {
        $request->validate([
            'peca_id' => 'required|exists:empacotamento_pecas,id',
            'nome_recebedor' => 'required|string|max:255',
            'assinatura' => 'nullable|string', // Base64 da assinatura
            'observacoes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $peca = EmpacotamentoPeca::with(['empacotamento.status'])->findOrFail($request->peca_id);

            // Verificar se a peça saiu com este motorista
            if ($peca->status_saida !== 'em_transito' || $peca->motorista_saida_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode entregar esta peça'
                ]);
            }

            // Verificar se já foi entregue
            if ($peca->data_entrega || $peca->motoristaEntrega) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta peça já foi entregue'
                ]);
            }

            // Salvar assinatura se fornecida
            $caminhoAssinatura = null;
            if ($request->assinatura) {
                $assinaturaData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->assinatura));
                $nomeArquivo = 'assinatura_peca_' . $peca->id . '_' . time() . '.png';
                $caminhoAssinatura = 'assinaturas/' . $nomeArquivo;

                \Storage::disk('public')->put($caminhoAssinatura, $assinaturaData);
            }

            // Atualizar a peça
            $peca->update([
                'status_saida' => 'entregue',
                'data_entrega' => now(),
                'motorista_entrega_id' => Auth::id(),
                'nome_recebedor' => $request->nome_recebedor,
                'assinatura_recebedor' => $caminhoAssinatura,
                'observacoes' => $request->observacoes
            ]);

            // Verificar se todas as peças que saíram foram entregues
            $pecasPendentesEntrega = EmpacotamentoPeca::where('empacotamento_id', $peca->empacotamento_id)
                ->where('status_saida', 'em_transito')
                ->whereNull('data_entrega')
                ->count();

            // Se todas as peças que saíram foram entregues, finalizar empacotamento
            if ($pecasPendentesEntrega == 0) {
                $statusConfirmado = Status::where('nome', 'Confirmado pelo Cliente')->first();
                if (!$statusConfirmado) {
                    $statusConfirmado = Status::where('nome', 'Entregue')->first();
                }
                
                if ($statusConfirmado) {
                    $peca->empacotamento->update(['status_id' => $statusConfirmado->id]);
                    
                    // Atualizar registro de entrega do empacotamento com confirmação automática
                    $entrega = $peca->empacotamento->entrega;
                    if ($entrega) {
                        $entrega->update([
                            'status_id' => $statusConfirmado->id,
                            'data_entrega' => now(),
                            'data_confirmacao_recebimento' => now(), // Confirma automaticamente
                            'motorista_entrega_id' => Auth::id(),
                            'nome_recebedor' => $request->nome_recebedor,
                            'assinatura_recebedor' => $caminhoAssinatura,
                            'assinatura_cliente' => $caminhoAssinatura, // Usar a mesma assinatura
                            'observacoes_entrega' => $request->observacoes
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Entrega da peça confirmada com sucesso!',
                'peca' => $peca,
                'todas_pecas_entregues' => $pecasPendentesEntrega == 0
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar entrega: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirmar entrega de todas as peças do empacotamento
     */
    public function confirmarEntrega(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id',
            'nome_recebedor' => 'required|string|max:255',
            'assinatura' => 'nullable|string', // Base64 da assinatura
            'observacoes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $empacotamento = Empacotamento::findOrFail($request->empacotamento_id);

            // Verificar se está em trânsito com este motorista
            $entrega = $empacotamento->entrega;
            if (!$entrega || $entrega->status->nome !== 'Em trânsito' || $entrega->motorista_saida_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode entregar este empacotamento'
                ]);
            }

            // Buscar status "Confirmado pelo Cliente" para finalizar imediatamente
            $statusConfirmado = Status::where('nome', 'Confirmado pelo Cliente')->first();
            if (!$statusConfirmado) {
                // Se não encontrar, usar "Entregue" como fallback
                $statusConfirmado = Status::where('nome', 'Entregue')->first();
                if (!$statusConfirmado) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status de entrega não encontrado'
                    ]);
                }
            }

            // Salvar assinatura se fornecida
            $caminhoAssinatura = null;
            if ($request->assinatura) {
                $assinaturaData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->assinatura));
                $nomeArquivo = 'assinatura_' . $empacotamento->id . '_' . time() . '.png';
                $caminhoAssinatura = 'assinaturas/' . $nomeArquivo;

                \Storage::disk('public')->put($caminhoAssinatura, $assinaturaData);
            }

            // Atualizar todas as peças que saíram mas não foram entregues
            EmpacotamentoPeca::where('empacotamento_id', $empacotamento->id)
                ->where('status_saida', 'em_transito')
                ->whereNull('data_entrega')
                ->update([
                    'status_saida' => 'entregue',
                    'data_entrega' => now(),
                    'motorista_entrega_id' => Auth::id(),
                    'nome_recebedor' => $request->nome_recebedor,
                    'assinatura_recebedor' => $caminhoAssinatura,
                    'observacoes' => $request->observacoes
                ]);

            // Atualizar empacotamento para status finalizado
            $empacotamento->update(['status_id' => $statusConfirmado->id]);

            // Atualizar entrega com confirmação automática
            $entrega->update([
                'status_id' => $statusConfirmado->id,
                'data_entrega' => now(),
                'data_confirmacao_recebimento' => now(), // Confirma automaticamente
                'motorista_entrega_id' => Auth::id(),
                'nome_recebedor' => $request->nome_recebedor,
                'assinatura_recebedor' => $caminhoAssinatura,
                'assinatura_cliente' => $caminhoAssinatura, // Usar a mesma assinatura
                'observacoes_entrega' => $request->observacoes
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Entrega confirmada e finalizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar entrega: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Confirmar entrega de peças selecionadas (não todas de uma vez)
     */
    public function confirmarEntregaSelecionadas(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id',
            'pecas_ids' => 'required|array|min:1',
            'pecas_ids.*' => 'exists:empacotamento_pecas,id',
            'nome_recebedor' => 'required|string|max:255',
            'assinatura' => 'nullable|string',
            'observacoes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $empacotamento = Empacotamento::findOrFail($request->empacotamento_id);

            // Salvar assinatura se fornecida
            $caminhoAssinatura = null;
            if ($request->assinatura) {
                $assinaturaData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->assinatura));
                $nomeArquivo = 'assinatura_' . $empacotamento->id . '_' . time() . '.png';
                $caminhoAssinatura = 'assinaturas/' . $nomeArquivo;
                \Storage::disk('public')->put($caminhoAssinatura, $assinaturaData);
            }

            // Atualizar apenas as peças selecionadas
            EmpacotamentoPeca::whereIn('id', $request->pecas_ids)
                ->where('empacotamento_id', $empacotamento->id)
                ->where('status_saida', 'em_transito')
                ->update([
                    'status_saida' => 'entregue',
                    'data_entrega' => now(),
                    'motorista_entrega_id' => Auth::id(),
                    'nome_recebedor' => $request->nome_recebedor,
                    'assinatura_recebedor' => $caminhoAssinatura,
                    'observacoes' => $request->observacoes
                ]);

            // Verificar se todas as peças foram entregues
            $pecasPendentes = EmpacotamentoPeca::where('empacotamento_id', $empacotamento->id)
                ->where('status_saida', 'em_transito')
                ->whereNull('data_entrega')
                ->count();

            if ($pecasPendentes === 0) {
                // Todas entregues - finalizar
                $statusConfirmado = Status::where('nome', 'Confirmado pelo Cliente')->first()
                    ?? Status::where('nome', 'Entregue')->first();

                if ($statusConfirmado) {
                    $empacotamento->update(['status_id' => $statusConfirmado->id]);
                    
                    $entrega = $empacotamento->entrega;
                    if ($entrega) {
                        $entrega->update([
                            'status_id' => $statusConfirmado->id,
                            'data_entrega' => now(),
                            'data_confirmacao_recebimento' => now(),
                            'motorista_entrega_id' => Auth::id(),
                            'nome_recebedor' => $request->nome_recebedor,
                            'assinatura_recebedor' => $caminhoAssinatura,
                            'assinatura_cliente' => $caminhoAssinatura,
                            'observacoes_entrega' => $request->observacoes
                        ]);
                    }
                }

                $mensagem = '✅ Todas as peças foram entregues! Entrega finalizada.';
            } else {
                $totalEntregues = count($request->pecas_ids);
                $mensagem = "✅ {$totalEntregues} peça(s) entregue(s). Ainda restam {$pecasPendentes} peça(s) em trânsito.";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'pecas_pendentes' => $pecasPendentes
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar entrega: ' . $e->getMessage()
            ]);
        }
    }
}
