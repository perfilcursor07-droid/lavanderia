<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coleta;
use App\Models\ColetaPeca;
use App\Models\Estabelecimento;
use App\Models\Status;
use App\Models\Tipo;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coleta::with(['estabelecimento', 'usuario', 'status'])
                      ->orderBy('created_at', 'desc');

        // Filtro por tipo de coleta
        $tipoColeta = $request->get('tipo_coleta', 'todas');
        if ($tipoColeta && $tipoColeta !== 'todas') {
            $query->where('tipo_coleta', $tipoColeta);
        }

        // Filtros
        if ($request->filled('estabelecimento_id')) {
            $query->where('estabelecimento_id', $request->estabelecimento_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_agendamento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_agendamento', '<=', $request->data_fim);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('numero_coleta', 'like', "%{$busca}%")
                  ->orWhereHas('estabelecimento', function($eq) use ($busca) {
                      $eq->where('razao_social', 'like', "%{$busca}%")
                         ->orWhere('nome_fantasia', 'like', "%{$busca}%");
                  });
            });
        }

        $coletas = $query->paginate(15);
        $estabelecimentos = Estabelecimento::ativos()->orderBy('razao_social')->get();
        $status = Status::where('tipo', 'coleta')->orderBy('ordem')->get();

        return view('coletas.index', compact('coletas', 'estabelecimentos', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $estabelecimentos = Estabelecimento::ativos()->orderBy('razao_social')->get();
        $tipos = Tipo::ativos()->orderBy('nome')->get();
        $status = Status::where('tipo', 'coleta')
                       ->where('nome', 'Agendada')
                       ->first();

        // Buscar usuários com nível de acesso "Motorista"
        $motoristas = Usuario::whereHas('nivelAcesso', function($query) {
                                $query->where('nome', 'Motorista');
                            })
                            ->where('ativo', true)
                            ->orderBy('nome')
                            ->get();

        return view('coletas.create', compact('estabelecimentos', 'tipos', 'status', 'motoristas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validações básicas
        $rules = [
            'estabelecimento_id' => 'required|exists:estabelecimentos,id',
            'tipo_coleta' => 'required|in:agendada,imediata',
            'observacoes' => 'nullable|string',
            'acompanhante_id' => 'nullable|exists:usuarios,id',
        ];

        $messages = [
            'estabelecimento_id.required' => 'Selecione um estabelecimento.',
            'estabelecimento_id.exists' => 'Estabelecimento inválido.',
            'tipo_coleta.required' => 'Selecione o tipo de coleta.',
            'tipo_coleta.in' => 'Tipo de coleta inválido.',
            'acompanhante_id.exists' => 'Motorista selecionado inválido.',
        ];

        // Adicionar validação de data apenas se for coleta agendada
        if ($request->tipo_coleta === 'agendada') {
            $rules['data_agendamento'] = 'required|date|after_or_equal:today';
            $messages['data_agendamento.required'] = 'A data de agendamento é obrigatória para coletas agendadas.';
            $messages['data_agendamento.date'] = 'Data de agendamento inválida.';
            $messages['data_agendamento.after_or_equal'] = 'A data deve ser hoje ou futura.';
        }

        $request->validate($rules, $messages);

        try {
            // Determinar status inicial baseado no tipo de coleta
            if ($request->tipo_coleta === 'agendada') {
                $statusInicial = Status::where('tipo', 'coleta')
                                      ->where('nome', 'Agendada')
                                      ->first();
                $dataAgendamento = $request->data_agendamento;
                $mensagemSucesso = 'Coleta agendada com sucesso! Você pode visualizar e gerenciar suas coletas na lista.';
            } else {
                // Para coleta imediata, usar status "Disponível para Coleta" ou similar
                $statusInicial = Status::where('tipo', 'coleta')
                                      ->where('nome', 'Coletado')
                                      ->first();
                
                // Se não encontrar status "Coletado", usar "Agendada" como fallback
                if (!$statusInicial) {
                    $statusInicial = Status::where('tipo', 'coleta')
                                          ->where('nome', 'Agendada')
                                          ->first();
                }
                
                $dataAgendamento = now(); // Data atual para coletas imediatas
                $mensagemSucesso = 'Coleta criada com sucesso! Esta coleta está disponível para execução imediata.';
            }

            // Buscar nome do motorista se selecionado
            $nomeAcompanhante = null;
            if ($request->acompanhante_id) {
                $motorista = Usuario::find($request->acompanhante_id);
                $nomeAcompanhante = $motorista ? $motorista->nome : null;
            }

            // Determinar tipo de coleta baseado nos parâmetros
            $tipoColeta = $request->input('tipo_processo', 'normal'); // normal, desengoma, relave
            $dataPrazoEntrega = null;
            
            // Para desengoma, definir prazo especial
            if ($tipoColeta === 'desengoma') {
                $dataPrazoEntrega = $request->input('data_prazo_entrega') 
                    ? Carbon::parse($request->input('data_prazo_entrega'))
                    : Carbon::parse($dataAgendamento)->addDays(7); // 7 dias por padrão
            }

            // Criar coleta
            $coleta = Coleta::create([
                'estabelecimento_id' => $request->estabelecimento_id,
                'usuario_id' => Auth::id(),
                'status_id' => $statusInicial->id,
                'data_agendamento' => $dataAgendamento,
                'observacoes' => $request->observacoes,
                'acompanhante' => $nomeAcompanhante,
                'tipo_coleta' => $tipoColeta,
                'data_prazo_entrega' => $dataPrazoEntrega,
            ]);

            return redirect()->route('coletas.show', $coleta->id)
                           ->with('success', $mensagemSucesso);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao criar coleta: ' . $e->getMessage());
        }
    }

    /**
     * Show form to add pieces to collection
     */
    public function addPecas($id)
    {
        $coleta = Coleta::with(['estabelecimento', 'status', 'pecas.tipo'])->findOrFail($id);
        $tipos = Tipo::ativos()->orderBy('nome')->get();

        return view('coletas.add-pecas', compact('coleta', 'tipos'));
    }

    /**
     * Store pieces for collection
     */
    public function storePecas(Request $request, $id)
    {
        $coleta = Coleta::findOrFail($id);

        $request->validate([
            'pecas' => 'required|array|min:1',
            'pecas.*.tipo_id' => 'nullable|exists:tipos,id',
            'pecas.*.modo_coleta' => 'required|in:quantidade,peso',
            'pecas.*.quantidade' => 'nullable|integer|min:1',
            'pecas.*.peso' => 'nullable|numeric|min:0.01',
        ], [
            'pecas.required' => 'Adicione pelo menos uma peça.',
            'pecas.min' => 'Adicione pelo menos uma peça.',
            'pecas.*.tipo_id.exists' => 'Tipo de peça inválido.',
            'pecas.*.modo_coleta.required' => 'Selecione o modo de coleta.',
            'pecas.*.modo_coleta.in' => 'Modo de coleta inválido.',
            'pecas.*.quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'pecas.*.quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'pecas.*.peso.numeric' => 'O peso deve ser um número.',
            'pecas.*.peso.min' => 'O peso deve ser maior que 0.',
        ]);

        // Validação customizada baseada no modo de coleta
        foreach ($request->pecas as $index => $pecaData) {
            if ($pecaData['modo_coleta'] === 'quantidade') {
                if (empty($pecaData['quantidade'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["pecas.{$index}.quantidade" => 'A quantidade é obrigatória no modo por quantidade.']);
                }
                if (empty($pecaData['tipo_id'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["pecas.{$index}.tipo_id" => 'Selecione o tipo da peça.']);
                }
            }
            if ($pecaData['modo_coleta'] === 'peso' && empty($pecaData['peso'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(["pecas.{$index}.peso" => 'O peso é obrigatório no modo por peso.']);
            }
        }

        DB::beginTransaction();
        try {
            // Remover peças existentes se houver
            $coleta->pecas()->delete();

            // Agrupar peças por tipo_id para somar duplicadas
            $pecasAgrupadas = [];
            foreach ($request->pecas as $pecaData) {
                $quantidade = $pecaData['modo_coleta'] === 'quantidade' ? intval($pecaData['quantidade']) : 0;
                $peso = $pecaData['modo_coleta'] === 'peso' ? floatval($pecaData['peso']) : 0;

                // Se for coleta por peso, usar o tipo especial "Peso"
                $tipoId = $pecaData['modo_coleta'] === 'peso'
                    ? \App\Models\Tipo::getTipoPeso()->id
                    : $pecaData['tipo_id'];

                $key = $tipoId . '_' . $pecaData['modo_coleta'];

                if (isset($pecasAgrupadas[$key])) {
                    // Somar quantidade e peso se o mesmo tipo já existe
                    $pecasAgrupadas[$key]['quantidade'] += $quantidade;
                    $pecasAgrupadas[$key]['peso'] += $peso;
                    // Concatenar observações se houver
                    if (!empty($pecaData['observacoes'])) {
                        $pecasAgrupadas[$key]['observacoes'] = trim(
                            ($pecasAgrupadas[$key]['observacoes'] ?? '') . ' | ' . $pecaData['observacoes'],
                            ' | '
                        );
                    }
                } else {
                    $pecasAgrupadas[$key] = [
                        'tipo_id' => $tipoId,
                        'quantidade' => $quantidade,
                        'peso' => $peso,
                        'observacoes' => $pecaData['observacoes'] ?? null,
                    ];
                }
            }

            // Criar peças da coleta (já agrupadas)
            foreach ($pecasAgrupadas as $pecaData) {
                ColetaPeca::create([
                    'coleta_id' => $coleta->id,
                    'tipo_id' => $pecaData['tipo_id'],
                    'quantidade' => $pecaData['quantidade'],
                    'peso' => $pecaData['peso'],
                    'observacoes' => $pecaData['observacoes'],
                ]);
            }

            // Calcular totais da coleta após criar todas as peças
            $coleta->calcularTotais();

            DB::commit();

            return redirect()->route('coletas.show', $coleta->id)
                           ->with('success', 'Peças adicionadas com sucesso! Coleta finalizada.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao adicionar Itens: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $coleta = Coleta::with(['estabelecimento', 'usuario', 'status', 'pecas.tipo', 'empacotamento'])
                       ->findOrFail($id);

        return view('coletas.show', compact('coleta'));
    }

    /**
     * Cancelar uma coleta
     */
    public function cancelar(Request $request, $id)
    {
        $request->validate([
            'motivo_cancelamento' => 'required|string|max:500',
        ], [
            'motivo_cancelamento.required' => 'O motivo do cancelamento é obrigatório.',
            'motivo_cancelamento.max' => 'O motivo deve ter no máximo 500 caracteres.',
        ]);

        $coleta = Coleta::findOrFail($id);

        if (!$coleta->podeSerCancelada()) {
            return redirect()->back()
                           ->with('error', 'Esta coleta não pode ser cancelada.');
        }

        $statusCancelada = Status::where('tipo', 'coleta')
                                ->where('nome', 'Cancelada')
                                ->first();

        $coleta->update([
            'status_id' => $statusCancelada->id,
            'motivo_cancelamento' => $request->motivo_cancelamento,
        ]);

        return redirect()->route('coletas.show', $coleta->id)
                       ->with('success', 'Coleta cancelada com sucesso.');
    }

    /**
     * Concluir uma coleta
     */
    public function concluir(Request $request, $id)
    {
        $coleta = Coleta::with('pecas')->findOrFail($id);

        // Verificar se a coleta pode ser concluída
        if (!$coleta->podeSerCancelada() || $coleta->status->nome === 'Concluída') {
            return redirect()->back()
                           ->with('error', 'Esta coleta não pode ser concluída.');
        }

        // Verificar se tem peças cadastradas
        if ($coleta->pecas->count() === 0) {
            // Se não tem peças e não foi forçada a conclusão, retornar erro
            if (!$request->has('forcar_conclusao')) {
                return redirect()->back()
                               ->with('warning', 'Esta coleta não possui peças cadastradas. Para concluir mesmo assim, confirme a ação.')
                               ->with('show_force_completion', true);
            }
        }

        $statusConcluida = Status::where('tipo', 'coleta')
                                ->where('nome', 'Concluída')
                                ->first();

        if (!$statusConcluida) {
            return redirect()->back()
                           ->with('error', 'Status "Concluída" não encontrado no sistema.');
        }

        $coleta->update([
            'status_id' => $statusConcluida->id,
            'data_coleta' => now(),
            'data_conclusao' => now(),
        ]);

        $mensagem = $coleta->pecas->count() === 0 
            ? 'Coleta concluída com sucesso (sem peças cadastradas).'
            : 'Coleta concluída com sucesso.';

        return redirect()->route('coletas.show', $coleta->id)
                       ->with('success', $mensagem);
    }

    /**
     * API: Buscar coletas por estabelecimento
     */
    public function getColetasPorEstabelecimento($estabelecimento_id)
    {
        $coletas = Coleta::where('estabelecimento_id', $estabelecimento_id)
                        ->with(['status', 'pecas.tipo'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json($coletas);
    }

    /**
     * API: Buscar peças de uma coleta
     */
    public function getPecasColeta($id)
    {
        $coleta = Coleta::with(['pecas.tipo'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'coleta' => [
                'id' => $coleta->id,
                'numero_coleta' => $coleta->numero_coleta,
                'peso_total' => $coleta->peso_total,
            ],
            'pecas' => $coleta->pecas->map(function($peca) {
                return [
                    'id' => $peca->id,
                    'tipo_id' => $peca->tipo_id,
                    'quantidade' => $peca->quantidade,
                    'peso' => $peca->peso,
                    'observacoes' => $peca->observacoes,
                    'tipo' => [
                        'id' => $peca->tipo->id,
                        'nome' => $peca->tipo->nome,
                        'categoria' => $peca->tipo->categoria,
                    ]
                ];
            })
        ]);
    }

    /**
     * API: Buscar detalhes de uma coleta
     */
    public function getDetalhesColeta($id)
    {
        $coleta = Coleta::with(['estabelecimento', 'usuario', 'status', 'pecas.tipo'])
                       ->findOrFail($id);

        // Garantir que os campos de preço estão sendo retornados
        if ($coleta->estabelecimento) {
            $coleta->estabelecimento->makeVisible(['tipo_precificacao', 'preco_kg', 'preco_peca']);
        }

        return response()->json([
            'coleta' => $coleta,
            'estabelecimento' => $coleta->estabelecimento
        ]);
    }

    /**
     * API: Buscar tipos de peças
     */
    public function getTipos()
    {
        $tipos = Tipo::ativos()->orderBy('nome')->get();
        return response()->json($tipos);
    }

    /**
     * API: Buscar dados atualizados das coletas
     */
    public function getColetasAtualizadas(Request $request)
    {
        $query = Coleta::with(['estabelecimento', 'usuario', 'status'])
                      ->orderBy('created_at', 'desc');

        // Aplicar os mesmos filtros da index
        if ($request->filled('estabelecimento_id')) {
            $query->where('estabelecimento_id', $request->estabelecimento_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_agendamento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_agendamento', '<=', $request->data_fim);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('numero_coleta', 'like', "%{$busca}%")
                  ->orWhereHas('estabelecimento', function($eq) use ($busca) {
                      $eq->where('razao_social', 'like', "%{$busca}%")
                         ->orWhere('nome_fantasia', 'like', "%{$busca}%");
                  });
            });
        }

        $coletas = $query->paginate(15);

        // Calcular estatísticas
        $stats = [
            'total_coletas' => Coleta::count(),
            'em_andamento' => Coleta::whereHas('status', function($q) {
                $q->where('nome', 'Em andamento');
            })->count(),
            'concluidas' => Coleta::whereHas('status', function($q) {
                $q->where('nome', 'Concluída');
            })->count(),
            'mes_atual' => Coleta::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count()
        ];

        return response()->json([
            'coletas' => $coletas,
            'stats' => $stats,
            'timestamp' => now()->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Criar coleta específica para DESENGOMA
     */
    public function createDesengoma()
    {
        $estabelecimentos = Estabelecimento::ativos()->orderBy('razao_social')->get();
        $tipos = Tipo::ativos()->orderBy('nome')->get();
        $status = Status::where('tipo', 'coleta')
                       ->where('nome', 'Agendada')
                       ->first();

        // Buscar usuários com nível de acesso "Motorista"
        $motoristas = Usuario::whereHas('nivelAcesso', function($query) {
                                $query->where('nome', 'Motorista');
                            })
                            ->where('ativo', true)
                            ->orderBy('nome')
                            ->get();

        return view('coletas.create-desengoma', compact('estabelecimentos', 'tipos', 'status', 'motoristas'));
    }

    /**
     * Armazenar coleta de DESENGOMA
     */
    public function storeDesengoma(Request $request)
    {
        $rules = [
            'estabelecimento_id' => 'required|exists:estabelecimentos,id',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'data_prazo_entrega' => 'required|date|after:data_agendamento',
            'observacoes' => 'nullable|string',
            'acompanhante_id' => 'nullable|exists:usuarios,id',
            'pecas' => 'required|array|min:1',
            'pecas.*.tipo_id' => 'required|exists:tipos,id',
            'pecas.*.quantidade' => 'required|integer|min:1',
            'pecas.*.peso' => 'nullable|numeric|min:0',
        ];

        $messages = [
            'estabelecimento_id.required' => 'Selecione um estabelecimento.',
            'data_agendamento.required' => 'A data de agendamento é obrigatória.',
            'data_agendamento.after_or_equal' => 'A data deve ser hoje ou futura.',
            'data_prazo_entrega.required' => 'A data de prazo de entrega é obrigatória para desengoma.',
            'data_prazo_entrega.after' => 'O prazo de entrega deve ser posterior à data de agendamento.',
            'pecas.required' => 'Adicione pelo menos uma peça.',
            'pecas.*.tipo_id.required' => 'Selecione o tipo da peça.',
            'pecas.*.quantidade.required' => 'A quantidade é obrigatória.',
            'pecas.*.quantidade.min' => 'A quantidade deve ser pelo menos 1.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            // Status inicial para desengoma
            $statusInicial = Status::where('tipo', 'coleta')
                                  ->where('nome', 'Agendada')
                                  ->first();

            // Buscar nome do motorista se selecionado
            $nomeAcompanhante = null;
            if ($request->acompanhante_id) {
                $motorista = Usuario::find($request->acompanhante_id);
                $nomeAcompanhante = $motorista ? $motorista->nome : null;
            }

            // Criar coleta de desengoma
            $coleta = Coleta::create([
                'estabelecimento_id' => $request->estabelecimento_id,
                'usuario_id' => Auth::id(),
                'status_id' => $statusInicial->id,
                'data_agendamento' => $request->data_agendamento,
                'observacoes' => $request->observacoes,
                'acompanhante' => $nomeAcompanhante,
                'tipo_coleta' => 'desengoma',
                'data_prazo_entrega' => $request->data_prazo_entrega,
            ]);

            // Criar peças da coleta (todas marcadas como desengoma)
            foreach ($request->pecas as $pecaData) {
                ColetaPeca::create([
                    'coleta_id' => $coleta->id,
                    'tipo_id' => $pecaData['tipo_id'],
                    'quantidade' => $pecaData['quantidade'],
                    'peso' => $pecaData['peso'] ?? 0,
                    'observacoes' => $pecaData['observacoes'] ?? 'Peça para desengoma - primeira lavagem',
                    'desengoma' => true, // Marcar como desengoma
                ]);
            }

            // Calcular totais da coleta
            $coleta->calcularTotais();

            DB::commit();

            return redirect()->route('coletas.index')
                           ->with('success', 'Coleta de DESENGOMA criada com sucesso! Prazo de entrega: ' . 
                                           Carbon::parse($request->data_prazo_entrega)->format('d/m/Y'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao criar coleta de desengoma: ' . $e->getMessage());
        }
    }

    /**
     * Criar coleta específica para RELAVE
     */
    public function createRelave()
    {
        $estabelecimentos = Estabelecimento::ativos()->orderBy('razao_social')->get();
        $tipos = Tipo::ativos()->orderBy('nome')->get();
        $status = Status::where('tipo', 'coleta')
                       ->where('nome', 'Agendada')
                       ->first();

        // Buscar usuários com nível de acesso "Motorista"
        $motoristas = Usuario::whereHas('nivelAcesso', function($query) {
                                $query->where('nome', 'Motorista');
                            })
                            ->where('ativo', true)
                            ->orderBy('nome')
                            ->get();

        return view('coletas.create-relave', compact('estabelecimentos', 'tipos', 'status', 'motoristas'));
    }

    /**
     * Armazenar coleta de RELAVE
     */
    public function storeRelave(Request $request)
    {
        $rules = [
            'estabelecimento_id' => 'required|exists:estabelecimentos,id',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'observacoes' => 'nullable|string',
            'acompanhante_id' => 'nullable|exists:usuarios,id',
            'pecas' => 'required|array|min:1',
            'pecas.*.tipo_id' => 'required|exists:tipos,id',
            'pecas.*.quantidade' => 'required|integer|min:1',
            'pecas.*.codigo_etiqueta_original' => 'nullable|string',
        ];

        $messages = [
            'estabelecimento_id.required' => 'Selecione um estabelecimento.',
            'data_agendamento.required' => 'A data de agendamento é obrigatória.',
            'data_agendamento.after_or_equal' => 'A data deve ser hoje ou futura.',
            'pecas.required' => 'Adicione pelo menos uma peça relave.',
            'pecas.*.tipo_id.required' => 'Selecione o tipo da peça.',
            'pecas.*.quantidade.required' => 'A quantidade é obrigatória.',
            'pecas.*.quantidade.min' => 'A quantidade deve ser pelo menos 1.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            // Status inicial para relave
            $statusInicial = Status::where('tipo', 'coleta')
                                  ->where('nome', 'Agendada')
                                  ->first();

            // Buscar nome do motorista se selecionado
            $nomeAcompanhante = null;
            if ($request->acompanhante_id) {
                $motorista = Usuario::find($request->acompanhante_id);
                $nomeAcompanhante = $motorista ? $motorista->nome : null;
            }

            // Criar coleta de relave
            $coleta = Coleta::create([
                'estabelecimento_id' => $request->estabelecimento_id,
                'usuario_id' => Auth::id(),
                'status_id' => $statusInicial->id,
                'data_agendamento' => $request->data_agendamento,
                'observacoes' => $request->observacoes,
                'acompanhante' => $nomeAcompanhante,
                'tipo_coleta' => 'relave',
            ]);

            // Criar peças da coleta (todas marcadas como relave)
            foreach ($request->pecas as $pecaData) {
                ColetaPeca::create([
                    'coleta_id' => $coleta->id,
                    'tipo_id' => $pecaData['tipo_id'],
                    'quantidade' => $pecaData['quantidade'],
                    'peso' => $pecaData['peso'] ?? 0,
                    'observacoes' => $pecaData['observacoes'] ?? 'Peça relave - segunda lavagem',
                    'relave' => true, // Marcar como relave
                ]);
            }

            // Calcular totais da coleta
            $coleta->calcularTotais();

            DB::commit();

            return redirect()->route('coletas.index')
                           ->with('success', 'Coleta de RELAVE criada com sucesso! As peças não serão cobradas por serem relave.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao criar coleta de relave: ' . $e->getMessage());
        }
    }

    /**
     * Listar coletas por tipo
     */
    public function indexPorTipo(Request $request, $tipo = 'normal')
    {
        $tiposPermitidos = ['normal', 'desengoma', 'relave'];
        
        if (!in_array($tipo, $tiposPermitidos)) {
            $tipo = 'normal';
        }

        $query = Coleta::with(['estabelecimento', 'usuario', 'status'])
                      ->where('tipo_coleta', $tipo)
                      ->orderBy('created_at', 'desc');

        // Aplicar filtros se houver
        if ($request->filled('estabelecimento_id')) {
            $query->where('estabelecimento_id', $request->estabelecimento_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_agendamento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_agendamento', '<=', $request->data_fim);
        }

        // Para desengoma, mostrar também as com prazo vencendo
        if ($tipo === 'desengoma') {
            $query->when($request->filled('prazo_vencendo'), function($q) {
                return $q->where('data_prazo_entrega', '<=', Carbon::now()->addDays(2));
            });
        }

        $coletas = $query->paginate(15);
        $estabelecimentos = Estabelecimento::ativos()->orderBy('razao_social')->get();
        $status = Status::where('tipo', 'coleta')->orderBy('ordem')->get();

        // Estatísticas específicas por tipo
        $stats = [
            'total' => Coleta::where('tipo_coleta', $tipo)->count(),
            'em_andamento' => Coleta::where('tipo_coleta', $tipo)
                                   ->whereHas('status', function($q) {
                                       $q->where('nome', 'Em andamento');
                                   })->count(),
            'concluidas' => Coleta::where('tipo_coleta', $tipo)
                                 ->whereHas('status', function($q) {
                                     $q->where('nome', 'Concluída');
                                 })->count(),
        ];

        // Para desengoma, adicionar estatística de prazo
        if ($tipo === 'desengoma') {
            $stats['prazo_vencendo'] = Coleta::where('tipo_coleta', 'desengoma')
                                            ->where('data_prazo_entrega', '<=', Carbon::now()->addDays(2))
                                            ->whereNotIn('status_id', function($query) {
                                                $query->select('id')
                                                      ->from('status')
                                                      ->where('nome', 'Concluída');
                                            })
                                            ->count();
        }

        return view('coletas.index-tipo', compact('coletas', 'estabelecimentos', 'status', 'tipo', 'stats'));
    }

    /**
     * Obter estatísticas de coletas por tipo
     */
    public function getEstatisticasTipo()
    {
        $stats = [
            'normal' => [
                'total' => Coleta::normal()->count(),
                'em_andamento' => Coleta::normal()->whereHas('status', function($q) {
                    $q->where('nome', 'Em andamento');
                })->count(),
                'concluidas' => Coleta::normal()->whereHas('status', function($q) {
                    $q->where('nome', 'Concluída');
                })->count(),
            ],
            'desengoma' => [
                'total' => Coleta::desengoma()->count(),
                'em_andamento' => Coleta::desengoma()->whereHas('status', function($q) {
                    $q->where('nome', 'Em andamento');
                })->count(),
                'concluidas' => Coleta::desengoma()->whereHas('status', function($q) {
                    $q->where('nome', 'Concluída');
                })->count(),
                'prazo_vencendo' => Coleta::desengoma()
                                         ->where('data_prazo_entrega', '<=', Carbon::now()->addDays(2))
                                         ->whereNotIn('status_id', function($query) {
                                             $query->select('id')
                                                   ->from('status')
                                                   ->where('nome', 'Concluída');
                                         })
                                         ->count(),
            ],
            'relave' => [
                'total' => Coleta::relave()->count(),
                'em_andamento' => Coleta::relave()->whereHas('status', function($q) {
                    $q->where('nome', 'Em andamento');
                })->count(),
                'concluidas' => Coleta::relave()->whereHas('status', function($q) {
                    $q->where('nome', 'Concluída');
                })->count(),
            ],
        ];

        return response()->json(['stats' => $stats]);
    }
}
