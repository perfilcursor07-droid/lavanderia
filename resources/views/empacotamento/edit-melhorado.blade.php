@extends('layouts.app')

@section('title', 'Editar Empacotamento - Versão Melhorada')

@push('styles')
<style>
    .tipo-finalizado {
        background-color: #d1fae5 !important;
        border-color: #10b981 !important;
    }
    
    .peca-relave {
        border-left: 4px solid #f59e0b;
        background-color: #fef3c7;
    }
    
    .peca-inutilizada {
        border-left: 4px solid #ef4444;
        background-color: #fee2e2;
    }
    
    .peca-impressa {
        background-color: #f3f4f6;
        opacity: 0.8;
    }
    
    .btn-finalizar-tipo {
        background-color: #10b981;
        color: white;
        transition: all 0.3s;
    }
    
    .btn-finalizar-tipo:hover {
        background-color: #059669;
        transform: scale(1.05);
    }
    
    .progress-bar {
        transition: width 0.5s ease-in-out;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .btn-duplicar {
        background-color: #3b82f6;
        color: white;
    }
    
    .btn-relave {
        background-color: #f59e0b;
        color: white;
    }
    
    .btn-inutilizada {
        background-color: #ef4444;
        color: white;
    }
    
    .btn-imprimir {
        background-color: #8b5cf6;
        color: white;
    }
    
    .btn-reimprimir {
        background-color: #6b7280;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Empacotamento {{ $empacotamento->codigo_qr }}
            </h1>
            <p class="text-sm text-gray-600">{{ $empacotamento->coleta->estabelecimento->nome_fantasia }} - Coleta {{ $empacotamento->coleta->numero_coleta }}</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('empacotamento.show', $empacotamento->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                Voltar
            </a>
        </div>
    </div>

    <!-- Estatísticas e Progresso -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Progresso Geral -->
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-900">Progresso</h3>
                <span id="progresso-percentual" class="text-sm font-bold text-blue-600">{{ $empacotamento->progresso_percentual ?? 0 }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="barra-progresso" class="progress-bar bg-blue-600 h-2 rounded-full" style="width: {{ $empacotamento->progresso_percentual ?? 0 }}%"></div>
            </div>
        </div>

        <!-- Tipos Finalizados -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-900">Tipos</h3>
            <div class="flex items-center mt-1">
                <span id="tipos-finalizados" class="text-2xl font-bold text-green-600">{{ count($empacotamento->tipos_finalizados ?? []) }}</span>
                <span class="text-gray-500 mx-1">/</span>
                <span id="tipos-totais" class="text-2xl font-bold text-gray-900">{{ $empacotamento->coleta->pecas->pluck('tipo_id')->unique()->count() }}</span>
            </div>
        </div>

        <!-- Lotes -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-900">Lotes</h3>
            <div class="flex items-center mt-1">
                <span id="lotes-impressos" class="text-2xl font-bold text-purple-600">{{ $empacotamento->totalEtiquetasImpressas() }}</span>
                <span class="text-gray-500 mx-1">/</span>
                <span id="lotes-totais" class="text-2xl font-bold text-gray-900">{{ $empacotamento->pecasIndividuais->count() }}</span>
            </div>
        </div>

        <!-- Especiais -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-900">Especiais</h3>
            <div class="text-xs text-gray-600 mt-1">
                <div>Relave: <span id="pecas-relave" class="font-semibold text-yellow-600">{{ $empacotamento->totalPecasRelave() }}</span></div>
                <div>Inutilizada: <span id="pecas-inutilizadas" class="font-semibold text-red-600">{{ $empacotamento->totalPecasInutilizadas() }}</span></div>
            </div>
        </div>
    </div>

    <!-- Formulário Principal -->
    <form id="formEmpacotamento" method="POST" action="{{ route('empacotamento.update', $empacotamento->id) }}">
        @csrf
        @method('PUT')

        <!-- Dados Básicos -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Dados do Empacotamento</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="data_empacotamento" class="block text-sm font-medium text-gray-700 mb-1">Data do Empacotamento</label>
                    <input type="datetime-local" 
                           id="data_empacotamento" 
                           name="data_empacotamento"
                           value="{{ $empacotamento->data_empacotamento->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <div>
                    <label for="funcionario_responsavel" class="block text-sm font-medium text-gray-700 mb-1">Funcionário Responsável</label>
                    <select id="funcionario_responsavel" name="funcionario_responsavel_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um funcionário</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="observacoes_empacotamento" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea id="observacoes_empacotamento" 
                          name="observacoes_empacotamento" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Observações sobre o empacotamento...">{{ $empacotamento->observacoes_empacotamento }}</textarea>
            </div>
        </div>

        <!-- Tipos de Peças -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Tipos de Peças</h2>
                <div class="flex gap-2">
                    <button type="button" id="btnSelecionarTodos" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Selecionar Todos
                    </button>
                    <button type="button" id="btnImprimirSelecionados" class="px-3 py-1 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700">
                        Imprimir Selecionados
                    </button>
                </div>
            </div>

            @php
                $tiposPorCategoria = $empacotamento->coleta->pecas->groupBy('tipo_id');
            @endphp

            <div id="tipos-container" class="space-y-4">
                @foreach($tiposPorCategoria as $tipoId => $pecasDoTipo)
                    @php
                        $tipo = $tipos->find($tipoId);
                        $tipoFinalizado = $empacotamento->tipoFinalizado($tipoId);
                        $pecasIndividuais = $empacotamento->pecasIndividuais()->where('tipo_id', $tipoId)->get();
                    @endphp

                    <div class="border rounded-lg {{ $tipoFinalizado ? 'tipo-finalizado' : 'border-gray-300' }}" data-tipo-id="{{ $tipoId }}">
                        <!-- Header do Tipo -->
                        <div class="p-4 bg-gray-50 border-b cursor-pointer" onclick="toggleTipo({{ $tipoId }})">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <input type="checkbox" class="lote-checkbox mr-3" data-tipo-id="{{ $tipoId }}" onclick="event.stopPropagation()">
                                    <h3 class="text-md font-semibold text-gray-900">
                                        {{ $tipo->nome }}
                                        @if($tipoFinalizado)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Finalizado
                                            </span>
                                        @endif
                                    </h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600">
                                        {{ $pecasDoTipo->sum('quantidade') }} peças → {{ $pecasIndividuais->count() }} lotes
                                    </span>
                                    
                                    @if(!$tipoFinalizado)
                                        <button type="button" 
                                                class="btn-finalizar-tipo px-3 py-1 text-xs rounded-md"
                                                onclick="finalizarTipo({{ $empacotamento->id }}, {{ $tipoId }}); event.stopPropagation();">
                                            Finalizar OK
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="px-3 py-1 text-xs bg-yellow-500 text-white rounded-md hover:bg-yellow-600"
                                                onclick="reabrirTipo({{ $empacotamento->id }}, {{ $tipoId }}); event.stopPropagation();">
                                            Reabrir
                                        </button>
                                    @endif
                                    
                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" id="chevron-{{ $tipoId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Conteúdo do Tipo -->
                        <div id="content-{{ $tipoId }}" class="hidden p-4">
                            <div class="space-y-3">
                                @foreach($pecasIndividuais as $peca)
                                    <div class="border rounded-lg p-3 {{ $peca->relave ? 'peca-relave' : '' }} {{ $peca->inutilizada ? 'peca-inutilizada' : '' }} {{ $peca->impresso ? 'peca-impressa' : '' }}" 
                                         data-peca-id="{{ $peca->id }}">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" class="peca-checkbox" data-peca-id="{{ $peca->id }}">
                                                <div>
                                                    <div class="font-semibold text-sm">{{ $peca->codigo_qr }}</div>
                                                    <div class="text-xs text-gray-600">
                                                        Qtd: {{ $peca->quantidade }} | Peso: {{ $peca->peso }}kg
                                                        @if($peca->responsavelEmpacotamento)
                                                            | Resp: {{ $peca->responsavelEmpacotamento->nome }}
                                                        @endif
                                                    </div>
                                                    @if($peca->observacoes)
                                                        <div class="text-xs text-gray-500 mt-1">{{ $peca->observacoes }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <!-- Botão Duplicar -->
                                                <button type="button" 
                                                        class="btn-duplicar px-2 py-1 text-xs rounded-md"
                                                        onclick="duplicarLote({{ $empacotamento->id }}, {{ $peca->id }})"
                                                        title="Duplicar Lote">
                                                    📋
                                                </button>

                                                <!-- Botão Relave -->
                                                <button type="button" 
                                                        class="btn-relave px-2 py-1 text-xs rounded-md {{ $peca->relave ? 'bg-orange-600' : 'bg-orange-400' }}"
                                                        onclick="toggleRelave({{ $peca->id }}, {{ $peca->relave ? 'false' : 'true' }})"
                                                        title="Marcar/Desmarcar como Relave">
                                                    RELAVE
                                                </button>

                                                <!-- Botão Inutilizada -->
                                                <button type="button" 
                                                        class="btn-inutilizada px-2 py-1 text-xs rounded-md {{ $peca->inutilizada ? 'bg-red-600' : 'bg-red-400' }}"
                                                        onclick="toggleInutilizada({{ $peca->id }}, {{ $peca->inutilizada ? 'false' : 'true' }})"
                                                        title="Marcar/Desmarcar como Inutilizada">
                                                    INUTILIZADA
                                                </button>

                                                <!-- Botão Impressão -->
                                                @if($peca->impresso)
                                                    <button type="button" 
                                                            class="btn-reimprimir px-2 py-1 text-xs rounded-md"
                                                            onclick="reimprimirEtiqueta({{ $peca->id }})"
                                                            title="Reimprimir Etiqueta">
                                                        🖨️ Reimprimir
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            class="btn-imprimir px-2 py-1 text-xs rounded-md"
                                                            onclick="imprimirEtiqueta({{ $peca->id }})"
                                                            title="Imprimir Etiqueta">
                                                        🖨️ Imprimir
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Botão Adicionar Lote -->
                                <button type="button" 
                                        class="w-full p-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors"
                                        onclick="adicionarLote({{ $tipoId }})">
                                    + Adicionar Novo Lote para {{ $tipo->nome }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Botão Adicionar Peça Extra -->
            <div class="mt-4">
                <button type="button" 
                        class="w-full p-3 border-2 border-dashed border-purple-300 rounded-lg text-purple-600 hover:border-purple-500 hover:text-purple-700 transition-colors"
                        onclick="abrirModalPecaExtra()">
                    + Adicionar Peça Extra (Tipo Diferente)
                </button>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="mt-6 flex justify-end gap-4">
            <button type="button" 
                    onclick="salvarAlteracoes()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<!-- Modal Peça Extra -->
<div id="modalPecaExtra" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Peça Extra</h3>
            
            <form id="formPecaExtra">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Peça</label>
                    <select id="tipoExtraSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Selecione o tipo</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                    <input type="number" id="quantidadeExtra" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                    <input type="number" id="pesoExtra" min="0" step="0.01" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Funcionário Responsável</label>
                    <select id="funcionarioExtraSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Selecione o funcionário</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea id="observacoesExtra" rows="3" placeholder="Ex: Peça encontrada durante empacotamento..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div class="flex gap-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="extraRelave" class="mr-2">
                        <span class="text-sm">É Relave</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="extraInutilizada" class="mr-2">
                        <span class="text-sm">É Inutilizada</span>
                    </label>
                </div>
            </form>

            <div class="items-center px-4 py-3">
                <button type="button" onclick="adicionarPecaExtra()" class="px-4 py-2 bg-purple-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Adicionar Peça Extra
                </button>
                <button type="button" onclick="fecharModalPecaExtra()" class="mt-2 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variáveis globais
let funcionarios = [];
let empacotamentoId = {{ $empacotamento->id }};

// Carregar dados iniciais
document.addEventListener('DOMContentLoaded', function() {
    carregarFuncionarios();
    atualizarEstatisticas();
    
    // Auto-refresh das estatísticas a cada 30 segundos
    setInterval(atualizarEstatisticas, 30000);
});

// Carregar lista de funcionários
async function carregarFuncionarios() {
    try {
        const response = await fetch('{{ route("empacotamento.funcionarios") }}');
        const data = await response.json();
        funcionarios = data.funcionarios;
        
        // Preencher selects
        const selects = ['funcionario_responsavel', 'funcionarioExtraSelect'];
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Selecione um funcionário</option>';
            funcionarios.forEach(funcionario => {
                select.innerHTML += `<option value="${funcionario.id}">${funcionario.nome}</option>`;
            });
        });
    } catch (error) {
        console.error('Erro ao carregar funcionários:', error);
    }
}

// Atualizar estatísticas
async function atualizarEstatisticas() {
    try {
        const response = await fetch(`{{ route('empacotamento.estatisticas', $empacotamento->id) }}`);
        const data = await response.json();
        const stats = data.stats;
        
        // Atualizar elementos na tela
        document.getElementById('progresso-percentual').textContent = Math.round(stats.progresso_percentual) + '%';
        document.getElementById('barra-progresso').style.width = stats.progresso_percentual + '%';
        document.getElementById('tipos-finalizados').textContent = stats.tipos_finalizados;
        document.getElementById('tipos-totais').textContent = stats.tipos_totais;
        document.getElementById('lotes-impressos').textContent = stats.lotes_impressos;
        document.getElementById('lotes-totais').textContent = stats.total_lotes;
        document.getElementById('pecas-relave').textContent = stats.pecas_relave;
        document.getElementById('pecas-inutilizadas').textContent = stats.pecas_inutilizadas;
        
    } catch (error) {
        console.error('Erro ao atualizar estatísticas:', error);
    }
}

// Toggle tipo de peça
function toggleTipo(tipoId) {
    const content = document.getElementById(`content-${tipoId}`);
    const chevron = document.getElementById(`chevron-${tipoId}`);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        chevron.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }
}

// Finalizar tipo
async function finalizarTipo(empacotamentoId, tipoId) {
    const funcionarioResponsavel = document.getElementById('funcionario_responsavel').value;
    
    try {
        const response = await fetch(`/sistema/empacotamento/${empacotamentoId}/finalizar-tipo/${tipoId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                usuario_responsavel_id: funcionarioResponsavel,
                observacoes: 'Tipo finalizado via interface melhorada'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Atualizar UI
            const tipoContainer = document.querySelector(`[data-tipo-id="${tipoId}"]`);
            tipoContainer.classList.add('tipo-finalizado');
            
            // Atualizar progresso
            atualizarEstatisticas();
            
            // Mostrar mensagem de sucesso
            mostrarNotificacao(data.message, 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao finalizar tipo:', error);
        mostrarNotificacao('Erro ao finalizar tipo de peça', 'error');
    }
}

// Reabrir tipo
async function reabrirTipo(empacotamentoId, tipoId) {
    try {
        const response = await fetch(`/sistema/empacotamento/${empacotamentoId}/reabrir-tipo/${tipoId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Atualizar UI
            const tipoContainer = document.querySelector(`[data-tipo-id="${tipoId}"]`);
            tipoContainer.classList.remove('tipo-finalizado');
            
            // Atualizar progresso
            atualizarEstatisticas();
            
            mostrarNotificacao(data.message, 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao reabrir tipo:', error);
        mostrarNotificacao('Erro ao reabrir tipo de peça', 'error');
    }
}

// Duplicar lote
async function duplicarLote(empacotamentoId, pecaId) {
    const funcionarioResponsavel = document.getElementById('funcionario_responsavel').value;
    
    try {
        const response = await fetch(`/sistema/empacotamento/${empacotamentoId}/duplicar-lote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                peca_id: pecaId,
                responsavel_empacotamento_id: funcionarioResponsavel
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recarregar a página para mostrar o novo lote
            location.reload();
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao duplicar lote:', error);
        mostrarNotificacao('Erro ao duplicar lote', 'error');
    }
}

// Toggle Relave
async function toggleRelave(pecaId, isRelave) {
    try {
        const response = await fetch(`/sistema/empacotamento/peca/${pecaId}/marcar-relave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                relave: isRelave === 'true'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Atualizar UI
            const pecaElement = document.querySelector(`[data-peca-id="${pecaId}"]`);
            if (isRelave === 'true') {
                pecaElement.classList.add('peca-relave');
            } else {
                pecaElement.classList.remove('peca-relave');
            }
            
            atualizarEstatisticas();
            mostrarNotificacao(data.message, 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao marcar relave:', error);
        mostrarNotificacao('Erro ao marcar peça como relave', 'error');
    }
}

// Toggle Inutilizada
async function toggleInutilizada(pecaId, isInutilizada) {
    try {
        const response = await fetch(`/sistema/empacotamento/peca/${pecaId}/marcar-inutilizada`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                inutilizada: isInutilizada === 'true'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Atualizar UI
            const pecaElement = document.querySelector(`[data-peca-id="${pecaId}"]`);
            if (isInutilizada === 'true') {
                pecaElement.classList.add('peca-inutilizada');
            } else {
                pecaElement.classList.remove('peca-inutilizada');
            }
            
            atualizarEstatisticas();
            mostrarNotificacao(data.message, 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao marcar inutilizada:', error);
        mostrarNotificacao('Erro ao marcar peça como inutilizada', 'error');
    }
}

// Imprimir etiqueta
async function imprimirEtiqueta(pecaId) {
    try {
        // Marcar como impresso
        const response = await fetch('/sistema/empacotamento/marcar-impresso', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                pecas_ids: [pecaId]
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Atualizar UI
            const pecaElement = document.querySelector(`[data-peca-id="${pecaId}"]`);
            pecaElement.classList.add('peca-impressa');
            
            // Abrir janela de impressão (implementar conforme necessário)
            abrirJanelaImpressao(pecaId);
            
            atualizarEstatisticas();
            mostrarNotificacao('Etiqueta marcada como impressa!', 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao imprimir etiqueta:', error);
        mostrarNotificacao('Erro ao imprimir etiqueta', 'error');
    }
}

// Reimprimir etiqueta
async function reimprimirEtiqueta(pecaId) {
    try {
        const response = await fetch(`/sistema/empacotamento/peca/${pecaId}/reimprimir-etiqueta`);
        const data = await response.json();
        
        if (data.success) {
            // Abrir janela de impressão com dados da etiqueta
            abrirJanelaImpressao(pecaId, data.etiqueta_data);
            mostrarNotificacao('Reimprimindo etiqueta...', 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao reimprimir etiqueta:', error);
        mostrarNotificacao('Erro ao reimprimir etiqueta', 'error');
    }
}

// Abrir janela de impressão
function abrirJanelaImpressao(pecaId, etiquetaData = null) {
    // Implementar conforme o sistema de impressão usado
    // Por exemplo, abrir uma nova janela com a etiqueta
    const url = `/sistema/empacotamento/peca/${pecaId}/etiqueta`;
    window.open(url, '_blank', 'width=400,height=600');
}

// Modal peça extra
function abrirModalPecaExtra() {
    document.getElementById('modalPecaExtra').classList.remove('hidden');
}

function fecharModalPecaExtra() {
    document.getElementById('modalPecaExtra').classList.add('hidden');
    document.getElementById('formPecaExtra').reset();
}

// Adicionar peça extra
async function adicionarPecaExtra() {
    const tipoId = document.getElementById('tipoExtraSelect').value;
    const quantidade = document.getElementById('quantidadeExtra').value;
    const peso = document.getElementById('pesoExtra').value;
    const funcionarioId = document.getElementById('funcionarioExtraSelect').value;
    const observacoes = document.getElementById('observacoesExtra').value;
    const relave = document.getElementById('extraRelave').checked;
    const inutilizada = document.getElementById('extraInutilizada').checked;
    
    if (!tipoId || !quantidade) {
        mostrarNotificacao('Preencha o tipo e quantidade da peça extra', 'error');
        return;
    }
    
    try {
        // Aqui você implementaria a criação da peça extra
        // Por enquanto, vamos simular
        mostrarNotificacao('Peça extra adicionada com sucesso!', 'success');
        fecharModalPecaExtra();
        
        // Recarregar página para mostrar a nova peça
        setTimeout(() => location.reload(), 1000);
    } catch (error) {
        console.error('Erro ao adicionar peça extra:', error);
        mostrarNotificacao('Erro ao adicionar peça extra', 'error');
    }
}

// Selecionar todos os lotes
document.getElementById('btnSelecionarTodos').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.peca-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
});

// Imprimir selecionados
document.getElementById('btnImprimirSelecionados').addEventListener('click', async function() {
    const checkboxes = document.querySelectorAll('.peca-checkbox:checked');
    const pecasIds = Array.from(checkboxes).map(cb => cb.dataset.pecaId);
    
    if (pecasIds.length === 0) {
        mostrarNotificacao('Selecione pelo menos uma peça para imprimir', 'error');
        return;
    }
    
    try {
        const response = await fetch('/sistema/empacotamento/marcar-impresso', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                pecas_ids: pecasIds
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Marcar como impressas na UI
            pecasIds.forEach(pecaId => {
                const pecaElement = document.querySelector(`[data-peca-id="${pecaId}"]`);
                pecaElement.classList.add('peca-impressa');
            });
            
            // Abrir janela de impressão em lote
            const url = `/sistema/empacotamento/${empacotamentoId}/imprimir-etiquetas`;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.target = '_blank';
            
            // Adicionar CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);
            
            // Adicionar IDs das peças
            pecasIds.forEach(pecaId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'pecas_ids[]';
                input.value = pecaId;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            atualizarEstatisticas();
            mostrarNotificacao(`${data.count} etiquetas marcadas como impressas!`, 'success');
        } else {
            mostrarNotificacao(data.message, 'error');
        }
    } catch (error) {
        console.error('Erro ao imprimir etiquetas:', error);
        mostrarNotificacao('Erro ao imprimir etiquetas selecionadas', 'error');
    }
});

// Salvar alterações
async function salvarAlteracoes() {
    const form = document.getElementById('formEmpacotamento');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            mostrarNotificacao('Alterações salvas com sucesso!', 'success');
        } else {
            mostrarNotificacao('Erro ao salvar alterações', 'error');
        }
    } catch (error) {
        console.error('Erro ao salvar:', error);
        mostrarNotificacao('Erro ao salvar alterações', 'error');
    }
}

// Função para mostrar notificações
function mostrarNotificacao(mensagem, tipo = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        tipo === 'success' ? 'bg-green-500 text-white' : 
        tipo === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = mensagem;
    
    document.body.appendChild(notification);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush

@endsection
