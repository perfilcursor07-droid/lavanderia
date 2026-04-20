<?php

namespace App\Http\Controllers;

use App\Models\Empacotamento;
use App\Models\EmpacotamentoPeca;
use App\Models\EmpacotamentoEtapa;
use App\Models\Coleta;
use App\Models\ColetaPeca;
use App\Models\Usuario;
use App\Models\Status;
use App\Models\Tipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpacotamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Empacotamento::with([
                                  'coleta.estabelecimento',
                                  'coleta.pecas',
                                  'pecasIndividuais',
                                  'usuarioEmpacotamento',
                                  'motorista',
                                  'status'
                              ])
                              ->whereHas('coleta'); // Apenas empacotamentos com coleta válida

        // Filtros
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('motorista_id')) {
            $query->where('motorista_id', $request->motorista_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_empacotamento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_empacotamento', '<=', $request->data_fim);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('codigo_qr', 'like', "%{$busca}%")
                  ->orWhereHas('coleta', function($subQ) use ($busca) {
                      $subQ->where('numero_coleta', 'like', "%{$busca}%")
                           ->orWhereHas('estabelecimento', function($estQ) use ($busca) {
                               $estQ->where('razao_social', 'like', "%{$busca}%");
                           });
                  });
            });
        }

        // Filtro para lotes pendentes
        if ($request->filled('lotes_pendentes') && $request->lotes_pendentes == '1') {
            $query->whereHas('pecasIndividuais', function($q) {
                $q->where('quantidade', '=', 0);
            });
        }

        $empacotamentos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Dados para filtros
        $status = Status::whereIn('nome', [
            'Aguardando empacotamento',
            'Em empacotamento', 
            'Pronto para entrega',
            'Em trânsito',
            'Entregue'
        ])->get();

        // Buscar apenas motoristas (usuários com nível de acesso "Motorista")
        $motoristas = Usuario::where('ativo', true)
                            ->whereHas('nivelAcesso', function($q) {
                                $q->where('nome', 'Motorista');
                            })
                            ->orderBy('nome')
                            ->get();

        return view('empacotamento.index', compact('empacotamentos', 'status', 'motoristas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $coletaId = $request->get('coleta_id');
        $coleta = null;

        if ($coletaId) {
            $coleta = Coleta::with(['estabelecimento', 'pecas.tipo'])->findOrFail($coletaId);
        }

        // Buscar coletas que podem ser empacotadas (concluídas e não empacotadas)
        $coletas = Coleta::with(['estabelecimento', 'pecas.tipo'])
                        ->whereHas('status', function($q) {
                            $q->where('nome', 'Concluída');
                        })
                        ->whereDoesntHave('empacotamento')
                        ->orderBy('numero_coleta', 'desc')
                        ->get();

        $tipos = Tipo::ativos()->orderBy('nome')->get();

        return view('empacotamento.create', compact('coletas', 'coleta', 'tipos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'coleta_id' => 'required|exists:coletas,id',
            'data_empacotamento' => 'required|date',
            'observacoes_empacotamento' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            // Verificar se a coleta já foi empacotada
            $coletaJaEmpacotada = Empacotamento::where('coleta_id', $request->coleta_id)->exists();
            if ($coletaJaEmpacotada) {
                return back()->withErrors(['coleta_id' => 'Esta coleta já foi empacotada.']);
            }

            // Buscar status "Em empacotamento" para início (será mudado se tudo estiver empacotado)
            $statusEmEmpacotamento = Status::where('nome', 'Em empacotamento')->first();
            if (!$statusEmEmpacotamento) {
                return back()->withErrors(['status' => 'Status "Em empacotamento" não encontrado.']);
            }

            // Criar empacotamento
            $empacotamento = Empacotamento::create([
                'coleta_id' => $request->coleta_id,
                'usuario_empacotamento_id' => Auth::id(),
                'motorista_id' => null, // Motorista será definido na saída
                'status_id' => $statusEmEmpacotamento->id,
                'data_empacotamento' => $request->data_empacotamento,
                'observacoes_empacotamento' => $request->observacoes_empacotamento
            ]);

            // Processar peças do empacotamento
            if ($request->has('pecas') || $request->has('pecas_extras') || $request->has('pecas_duplicadas')) {
                // Empacotamento com dados de peças (vindo do formulário)
                $this->processarPecasEmpacotamento($request, $empacotamento);
            } else {
                // Empacotamento inicial - criar peças baseadas na coleta
                $this->criarPecasIniciaisEmpacotamento($empacotamento);
            }

            // Atualizar status da coleta para "Empacotada"
            $statusEmpacotada = Status::where('nome', 'Empacotada')->first();
            if ($statusEmpacotada) {
                $empacotamento->coleta->update(['status_id' => $statusEmpacotada->id]);
            }

            // Verificar se a quantidade empacotada está completa
            $empacotamento = $empacotamento->fresh(['coleta.pecas', 'pecasIndividuais']);
            
            if (!$empacotamento->estaEmAberto()) {
                // Se tudo foi empacotado, mudar para "Pronto para motorista"
                $statusProntoMotorista = Status::where('nome', 'Pronto para motorista')->first();
                if ($statusProntoMotorista) {
                    $empacotamento->update(['status_id' => $statusProntoMotorista->id]);
                }
            }

            DB::commit();

            // Se foi criação inicial (sem dados de peças), redirecionar para edição
            if (!$request->has('pecas') && !$request->has('pecas_extras') && !$request->has('pecas_duplicadas')) {
                return redirect()->route('empacotamento.edit', $empacotamento->id)
                               ->with('success', 'Empacotamento criado! Agora você pode ajustar as quantidades e dividir as peças conforme necessário.');
            }

            $mensagem = $empacotamento->estaEmAberto() 
                ? 'Empacotamento criado! Atenção: ainda há peças pendentes de empacotamento.'
                : 'Empacotamento criado com sucesso! Todas as peças foram empacotadas.';

            return redirect()->route('empacotamento.show', $empacotamento->id)
                           ->with('success', $mensagem);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erro ao criar empacotamento: ' . $e->getMessage()]);
        }
    }

    /**
     * Processar peças do empacotamento
     */
    private function processarPecasEmpacotamento(Request $request, Empacotamento $empacotamento)
    {
        $coleta = $empacotamento->coleta;

        // Verificar se há peças existentes para conferência (coleta por quantidade)
        if ($request->has('pecas')) {
            foreach ($request->pecas as $pecaId => $dadosEmpacotamento) {
                $coletaPeca = $coleta->pecas->find($pecaId);
                if ($coletaPeca) {
                    // Atualizar quantidade empacotada na peça da coleta
                    $coletaPeca->update([
                        'quantidade_empacotada' => $dadosEmpacotamento['quantidade_empacotada'] ?? 0,
                        'peso_empacotado' => $dadosEmpacotamento['peso_empacotado'] ?? 0,
                    ]);

                    // Criar peça individual do empacotamento com QR code
                    if (($dadosEmpacotamento['quantidade_empacotada'] ?? 0) > 0) {
                        EmpacotamentoPeca::create([
                            'empacotamento_id' => $empacotamento->id,
                            'tipo_id' => $coletaPeca->tipo_id,
                            'quantidade' => $dadosEmpacotamento['quantidade_empacotada'],
                            'peso' => $dadosEmpacotamento['peso_empacotado'] ?? 0,
                            'observacoes' => "Peça empacotada - Qtd. original: {$coletaPeca->quantidade}"
                        ]);
                    }
                }
            }
        }

        // Processar peças duplicadas (conferência de quantidade)
        if ($request->has('pecas_duplicadas')) {
            foreach ($request->pecas_duplicadas as $dadosDuplicada) {
                $coletaPecaOriginal = $coleta->pecas->find($dadosDuplicada['peca_original_id']);
                if ($coletaPecaOriginal && ($dadosDuplicada['quantidade_empacotada'] ?? 0) > 0) {
                    // Criar peça individual duplicada do empacotamento com QR code
                    EmpacotamentoPeca::create([
                        'empacotamento_id' => $empacotamento->id,
                        'tipo_id' => $coletaPecaOriginal->tipo_id,
                        'quantidade' => $dadosDuplicada['quantidade_empacotada'],
                        'peso' => $dadosDuplicada['peso_empacotado'] ?? 0,
                        'observacoes' => "Peça duplicada - Baseada na peça original ID: {$coletaPecaOriginal->id}"
                    ]);
                }
            }
        }

        // Processar peças extras (conferência de quantidade)
        if ($request->has('pecas_extras')) {
            foreach ($request->pecas_extras as $dadosExtra) {
                if (!empty($dadosExtra['tipo_id']) && ($dadosExtra['quantidade'] ?? 0) > 0) {
                    // Criar peça individual extra do empacotamento com QR code
                    EmpacotamentoPeca::create([
                        'empacotamento_id' => $empacotamento->id,
                        'tipo_id' => $dadosExtra['tipo_id'],
                        'quantidade' => $dadosExtra['quantidade'],
                        'peso' => $dadosExtra['peso'] ?? 0,
                        'observacoes' => 'Peça extra - Não estava na coleta original'
                    ]);
                }
            }
        }

        // Verificar se há novas peças para cadastrar (coleta por peso)
        if ($request->has('novas_pecas')) {
            // Para coletas por peso, substituir todas as peças existentes pelas novas
            // Remover peças existentes da coleta
            $coleta->pecas()->delete();

            // Criar novas peças baseadas no empacotamento
            foreach ($request->novas_pecas as $novaPeca) {
                if (!empty($novaPeca['tipo_id']) && !empty($novaPeca['quantidade'])) {
                    // Criar peça na coleta
                    ColetaPeca::create([
                        'coleta_id' => $coleta->id,
                        'tipo_id' => $novaPeca['tipo_id'],
                        'quantidade' => $novaPeca['quantidade'],
                        'peso' => 0, // Peso individual não é conhecido
                        'quantidade_empacotada' => $novaPeca['quantidade'],
                        'peso_empacotado' => 0,
                        'observacoes' => 'Tipos definidos no empacotamento (coleta foi por peso total)'
                    ]);

                    // Criar peça individual do empacotamento com QR code
                    EmpacotamentoPeca::create([
                        'empacotamento_id' => $empacotamento->id,
                        'tipo_id' => $novaPeca['tipo_id'],
                        'quantidade' => $novaPeca['quantidade'],
                        'peso' => $novaPeca['peso'] ?? 0,
                        'observacoes' => 'Peça empacotada - Coleta por peso total'
                    ]);
                }
            }
        }
    }

    /**
     * Processar atualizações das peças individuais do empacotamento
     */
    private function processarPecasIndividuaisEmpacotamento(Request $request, Empacotamento $empacotamento)
    {
        // Sempre processar todos os tipos (modo simplificado)
        \Log::info('Processando todos os tipos de peças');

        // Processar lotes removidos
        if ($request->has('lotes_removidos')) {
            foreach ($request->lotes_removidos as $pecaId) {
                $pecaIndividual = EmpacotamentoPeca::find($pecaId);
                if ($pecaIndividual && $pecaIndividual->empacotamento_id == $empacotamento->id) {
                    $pecaIndividual->delete();
                }
            }
        }

        // Verificar se há peças individuais para atualizar
        if ($request->has('pecas_individuais')) {
            foreach ($request->pecas_individuais as $pecaId => $dadosPeca) {
                $pecaIndividual = EmpacotamentoPeca::find($pecaId);

                if ($pecaIndividual && $pecaIndividual->empacotamento_id == $empacotamento->id) {
                    $pecaIndividual->update([
                        'quantidade' => $dadosPeca['quantidade'],
                        'peso' => $dadosPeca['peso'] ?? 0,
                    ]);
                }
            }
        }

        // Processar novos lotes
        if ($request->has('novos_lotes')) {
            foreach ($request->novos_lotes as $dadosLote) {
                // Só criar o registro se a quantidade for maior que 0
                if (($dadosLote['quantidade'] ?? 0) > 0) {
                    EmpacotamentoPeca::create([
                        'empacotamento_id' => $empacotamento->id,
                        'tipo_id' => $dadosLote['tipo_id'],
                        'quantidade' => $dadosLote['quantidade'],
                        'peso' => $dadosLote['peso'] ?? 0,
                        'observacoes' => "Lote adicional - Tipo: {$dadosLote['tipo_nome']}"
                    ]);
                }
            }
        }

        // Processar peças extras
        if ($request->has('pecas_extras')) {
            // Debug: vamos ver o que está chegando
            \Log::info('Peças extras recebidas:', $request->pecas_extras);

            foreach ($request->pecas_extras as $dadosExtra) {
                // Só criar o registro se a quantidade for maior que 0
                if (($dadosExtra['quantidade'] ?? 0) > 0) {
                    $pecaExtra = EmpacotamentoPeca::create([
                        'empacotamento_id' => $empacotamento->id,
                        'tipo_id' => $dadosExtra['tipo_id'],
                        'quantidade' => $dadosExtra['quantidade'],
                        'peso' => $dadosExtra['peso'] ?? 0,
                        'observacoes' => $dadosExtra['observacoes'] ?? "Peça extra - Tipo: {$dadosExtra['tipo_nome']}"
                    ]);

                    \Log::info('Peça extra criada:', $pecaExtra->toArray());
                }
            }
        } else {
            \Log::info('Nenhuma peça extra recebida no request');
        }
    }

    /**
     * Criar peças iniciais do empacotamento baseadas na coleta
     */
    private function criarPecasIniciaisEmpacotamento(Empacotamento $empacotamento)
    {
        $coleta = $empacotamento->coleta;

        // Inicializar as quantidades empacotadas na coleta_pecas
        // Inclui tanto peças por quantidade quanto por peso
        foreach ($coleta->pecas as $coletaPeca) {
            if ($coletaPeca->quantidade > 0 || $coletaPeca->peso > 0) {
                $coletaPeca->update([
                    'quantidade_empacotada' => 0,
                    'peso_empacotado' => 0,
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $empacotamento = Empacotamento::with([
            'coleta.estabelecimento',
            'coleta.pecas.tipo',
            'pecasIndividuais.tipo',
            'usuarioEmpacotamento',
            'motorista',
            'status'
        ])->findOrFail($id);

        return view('empacotamento.show', compact('empacotamento'));
    }

    /**
     * Confirmar entrega do empacotamento
     */
    public function confirmarEntrega(Request $request, $id)
    {
        $request->validate([
            'nome_recebedor' => 'required|string|max:255',
            'data_entrega' => 'required|date',
            'observacoes_entrega' => 'nullable|string|max:1000'
        ]);

        $empacotamento = Empacotamento::findOrFail($id);

        // Verificar se pode ser entregue
        if (!$empacotamento->podeSerEntregue()) {
            return back()->withErrors(['error' => 'Este empacotamento não pode ser entregue no status atual.']);
        }

        DB::beginTransaction();
        try {
            // Buscar status "Entregue"
            $statusEntregue = Status::where('nome', 'Entregue')->first();
            if (!$statusEntregue) {
                return back()->withErrors(['error' => 'Status "Entregue" não encontrado.']);
            }

            // Atualizar empacotamento
            $empacotamento->update([
                'status_id' => $statusEntregue->id,
                'data_entrega' => $request->data_entrega,
                'data_confirmacao_recebimento' => now(),
                'nome_recebedor' => $request->nome_recebedor,
                'observacoes_entrega' => $request->observacoes_entrega
            ]);

            // Atualizar status da coleta para "Entregue"
            $empacotamento->coleta->update(['status_id' => $statusEntregue->id]);

            DB::commit();

            return redirect()->route('empacotamento.show', $empacotamento->id)
                           ->with('success', 'Entrega confirmada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erro ao confirmar entrega: ' . $e->getMessage()]);
        }
    }

    /**
     * Reimprimir QR Code
     */
    public function reimprimirQR($id)
    {
        $empacotamento = Empacotamento::with(['coleta.estabelecimento'])->findOrFail($id);

        return view('empacotamento.qrcode', compact('empacotamento'));
    }

    /**
     * Gerar etiqueta do empacotamento para impressão
     */
    public function gerarEtiqueta($id)
    {
        $empacotamento = Empacotamento::with([
            'coleta.estabelecimento',
            'coleta.pecas.tipo',
            'usuarioEmpacotamento',
            'pecasIndividuais.tipo'
        ])->findOrFail($id);

        return view('empacotamento.etiqueta', compact('empacotamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $empacotamento = Empacotamento::with([
            'coleta.estabelecimento',
            'coleta.pecas.tipo',
            'pecasIndividuais.tipo',
            'usuarioEmpacotamento',
            'status'
        ])->findOrFail($id);

        // Verificar se pode ser editado
        if ($empacotamento->status->nome === 'Entregue') {
            return redirect()->route('empacotamento.show', $empacotamento->id)
                           ->with('error', 'Empacotamentos entregues não podem ser editados.');
        }

        $tipos = Tipo::ativos()->orderBy('nome')->get();

        return view('empacotamento.edit', compact('empacotamento', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $empacotamento = Empacotamento::findOrFail($id);

        // Verificar se pode ser editado
        if ($empacotamento->status->nome === 'Entregue') {
            return redirect()->back()
                           ->with('error', 'Empacotamentos entregues não podem ser editados.');
        }

        $request->validate([
            'data_empacotamento' => 'required|date',
            'observacoes_empacotamento' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            // Atualizar empacotamento
            $empacotamento->update([
                'data_empacotamento' => $request->data_empacotamento,
                'observacoes_empacotamento' => $request->observacoes_empacotamento
            ]);

            // Processar peças individuais atualizadas
            $this->processarPecasIndividuaisEmpacotamento($request, $empacotamento);

            // Verificar se quantidade empacotada está completa
            $empacotamento = $empacotamento->fresh(['coleta.pecas', 'pecasIndividuais']);
            
            // Verificar se o usuário quer forçar a finalização mesmo com diferença
            $forcarFinalizacao = $request->has('forcar_finalizacao');
            
            if ($empacotamento->estaEmAberto() && !$forcarFinalizacao) {
                // Se ainda falta empacotar, manter em "Em empacotamento"
                $statusEmEmpacotamento = Status::where('nome', 'Em empacotamento')->first();
                if ($statusEmEmpacotamento) {
                    $empacotamento->update(['status_id' => $statusEmEmpacotamento->id]);
                }
                
                $mensagem = 'Empacotamento atualizado! Atenção: ainda há peças pendentes de empacotamento.';
            } else {
                // Se tudo foi empacotado OU forçou finalização, marcar como "Pronto para motorista"
                $statusProntoMotorista = Status::where('nome', 'Pronto para motorista')->first();
                if ($statusProntoMotorista) {
                    $empacotamento->update(['status_id' => $statusProntoMotorista->id]);
                }
                
                if ($forcarFinalizacao && $empacotamento->estaEmAberto()) {
                    $mensagem = 'Empacotamento finalizado com diferença de peças. Pronto para o motorista.';
                } else {
                    $mensagem = 'Empacotamento atualizado com sucesso! Todas as peças foram empacotadas.';
                }
            }

            DB::commit();

            return redirect()->route('empacotamento.show', $empacotamento->id)
                           ->with('success', $mensagem);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erro ao atualizar empacotamento: ' . $e->getMessage()]);
        }
    }

    /**
     * Concluir empacotamento
     */
    public function concluir($id)
    {
        $empacotamento = Empacotamento::findOrFail($id);

        // Verificar se pode ser concluído
        if ($empacotamento->status->nome !== 'Pronto para motorista') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas empacotamentos "Pronto para motorista" podem ser concluídos.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Empacotamento já está "Pronto para motorista", não precisa alterar status
            // O status só mudará quando o motorista confirmar a saída
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Empacotamento concluído e pronto para o motorista!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao concluir empacotamento: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Finalizar tipo de peça no empacotamento
     */
    public function finalizarTipo(Request $request, $id, $tipoId)
    {
        $empacotamento = Empacotamento::findOrFail($id);
        
        if (!$empacotamento->podeSerEditado()) {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento não pode ser editado.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Finalizar tipo no empacotamento
            $empacotamento->finalizarTipo($tipoId);

            // Registrar etapa se fornecido usuário responsável
            if ($request->filled('usuario_responsavel_id')) {
                EmpacotamentoEtapa::create([
                    'empacotamento_id' => $empacotamento->id,
                    'tipo_id' => $tipoId,
                    'usuario_responsavel_id' => $request->usuario_responsavel_id,
                    'status' => 'finalizado',
                    'data_inicio' => now()->subMinutes(30), // Estimativa
                    'data_finalizacao' => now(),
                    'observacoes' => $request->observacoes
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de peça finalizado com sucesso!',
                'progresso' => $empacotamento->fresh()->progresso_percentual
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar tipo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reabrir tipo de peça no empacotamento
     */
    public function reabrirTipo($id, $tipoId)
    {
        $empacotamento = Empacotamento::findOrFail($id);
        
        if (!$empacotamento->podeSerEditado()) {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento não pode ser editado.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $empacotamento->reabrirTipo($tipoId);

            // Reabrir etapa correspondente
            $etapa = EmpacotamentoEtapa::where('empacotamento_id', $empacotamento->id)
                                    ->where('tipo_id', $tipoId)
                                    ->where('status', 'finalizado')
                                    ->first();
            
            if ($etapa) {
                $etapa->reabrir();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de peça reaberto com sucesso!',
                'progresso' => $empacotamento->fresh()->progresso_percentual
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reabrir tipo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicar lote no empacotamento
     */
    public function duplicarLote(Request $request, $id)
    {
        $empacotamento = Empacotamento::findOrFail($id);
        $pecaOriginal = EmpacotamentoPeca::findOrFail($request->peca_id);

        if ($pecaOriginal->empacotamento_id !== $empacotamento->id) {
            return response()->json([
                'success' => false,
                'message' => 'Peça não pertence a este empacotamento.'
            ], 403);
        }

        if (!$empacotamento->podeSerEditado()) {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento não pode ser editado.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $novaPeca = EmpacotamentoPeca::create([
                'empacotamento_id' => $empacotamento->id,
                'tipo_id' => $pecaOriginal->tipo_id,
                'quantidade' => $pecaOriginal->quantidade,
                'peso' => $pecaOriginal->peso,
                'observacoes' => 'Lote duplicado - Baseado no lote ' . $pecaOriginal->codigo_qr,
                'responsavel_empacotamento_id' => $request->responsavel_empacotamento_id ?? Auth::id(),
                'relave' => $pecaOriginal->relave,
                'inutilizada' => $pecaOriginal->inutilizada
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lote duplicado com sucesso!',
                'nova_peca' => [
                    'id' => $novaPeca->id,
                    'codigo_qr' => $novaPeca->codigo_qr,
                    'quantidade' => $novaPeca->quantidade,
                    'peso' => $novaPeca->peso
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao duplicar lote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar peça como relave
     */
    public function marcarRelave(Request $request, $pecaId)
    {
        $peca = EmpacotamentoPeca::findOrFail($pecaId);
        
        if (!$peca->empacotamento->podeSerEditado()) {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento não pode ser editado.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $peca->update([
                'relave' => $request->boolean('relave'),
                'observacoes' => $request->filled('observacoes') ? $request->observacoes : 
                    ($request->boolean('relave') ? 'Peça marcada como RELAVE' : 'Marca RELAVE removida')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->boolean('relave') ? 'Peça marcada como RELAVE' : 'Marca RELAVE removida'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar peça: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar peça como inutilizada
     */
    public function marcarInutilizada(Request $request, $pecaId)
    {
        $peca = EmpacotamentoPeca::findOrFail($pecaId);
        
        if (!$peca->empacotamento->podeSerEditado()) {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento não pode ser editado.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $peca->update([
                'inutilizada' => $request->boolean('inutilizada'),
                'observacoes' => $request->filled('observacoes') ? $request->observacoes : 
                    ($request->boolean('inutilizada') ? 'Peça marcada como INUTILIZADA' : 'Marca INUTILIZADA removida')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->boolean('inutilizada') ? 'Peça marcada como INUTILIZADA' : 'Marca INUTILIZADA removida'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar peça: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar etiqueta como impressa
     */
    public function marcarImpresso(Request $request)
    {
        $pecasIds = $request->input('pecas_ids', []);
        
        if (empty($pecasIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma peça selecionada para impressão.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $pecas = EmpacotamentoPeca::whereIn('id', $pecasIds)->get();
            
            foreach ($pecas as $peca) {
                $peca->marcarComoImpresso();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Etiquetas marcadas como impressas!',
                'count' => count($pecasIds)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar impressão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reimprimir etiqueta
     */
    public function reimprimirEtiqueta($pecaId)
    {
        $peca = EmpacotamentoPeca::with(['empacotamento.coleta.estabelecimento', 'tipo', 'responsavelEmpacotamento'])
                                ->findOrFail($pecaId);

        if (!$peca->podeSerReimpresso()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta etiqueta ainda não foi impressa.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'etiqueta_data' => [
                'codigo_qr' => $peca->codigo_qr,
                'url_qr' => $peca->url_qr_code,
                'descricao' => $peca->descricao_etiqueta,
                'hotel' => $peca->empacotamento->coleta->estabelecimento->nome_fantasia,
                'tipo' => $peca->tipo->nome,
                'quantidade' => $peca->quantidade,
                'data' => $peca->empacotamento->data_empacotamento->format('d/m/Y'),
                'responsavel' => $peca->responsavelEmpacotamento?->nome,
                'relave' => $peca->relave,
                'inutilizada' => $peca->inutilizada
            ]
        ]);
    }

    /**
     * Obter lista de funcionários disponíveis para empacotamento
     */
    public function getFuncionarios()
    {
        $funcionarios = Usuario::where('ativo', true)
                             ->whereHas('nivelAcesso', function($q) {
                                 $q->whereJsonContains('permissoes', 'empacotamento.criar')
                                   ->orWhereJsonContains('permissoes', 'empacotamento.editar');
                             })
                             ->orderBy('nome')
                             ->get(['id', 'nome']);

        return response()->json(['funcionarios' => $funcionarios]);
    }

    /**
     * Imprimir etiquetas de lotes selecionados
     */
    public function imprimirEtiquetas(Request $request, $id)
    {
        $empacotamento = Empacotamento::with(['coleta.estabelecimento'])->findOrFail($id);
        $pecasIds = $request->input('pecas_ids', []);

        if (empty($pecasIds)) {
            return back()->with('error', 'Nenhuma peça selecionada para impressão.');
        }

        $pecas = EmpacotamentoPeca::with(['tipo', 'responsavelEmpacotamento'])
                                 ->whereIn('id', $pecasIds)
                                 ->where('empacotamento_id', $empacotamento->id)
                                 ->get();

        return view('empacotamento.etiquetas-lote', compact('empacotamento', 'pecas'));
    }

    /**
     * Obter estatísticas do empacotamento
     */
    public function getEstatisticas($id)
    {
        $empacotamento = Empacotamento::with(['pecasIndividuais', 'coleta.pecas'])->findOrFail($id);

        $stats = [
            'progresso_percentual' => $empacotamento->progresso_percentual,
            'tipos_totais' => $empacotamento->coleta->pecas->pluck('tipo_id')->unique()->count(),
            'tipos_finalizados' => count($empacotamento->tipos_finalizados ?? []),
            'total_lotes' => $empacotamento->pecasIndividuais->count(),
            'lotes_impressos' => $empacotamento->totalEtiquetasImpressas(),
            'lotes_nao_impressos' => $empacotamento->totalEtiquetasNaoImpressas(),
            'pecas_relave' => $empacotamento->totalPecasRelave(),
            'pecas_inutilizadas' => $empacotamento->totalPecasInutilizadas(),
            'funcionarios_trabalhando' => $empacotamento->getFuncionariosEmpacotamento()->count()
        ];

        return response()->json(['stats' => $stats]);
    }
}
