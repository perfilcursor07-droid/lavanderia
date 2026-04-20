@extends('layouts.app')

@section('title', 'Editar Empacotamento')

@push('styles')
<style>
    /* Estilos para peças extras */
    .tipo-peca-container[data-tipo-id] {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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
                Editar Empacotamento {{ $empacotamento->codigo_qr }}
            </h1>
            <p class="text-sm text-gray-600">Modificar dados do empacotamento</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('empacotamento.show', $empacotamento->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <h4 class="font-medium mb-2">Corrija os seguintes erros:</h4>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('empacotamento.update', $empacotamento->id) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Informações do Empacotamento
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Data de Empacotamento -->
                            <div>
                                <label for="data_empacotamento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Data e Hora do Empacotamento *
                                </label>
                                <input type="datetime-local" 
                                       id="data_empacotamento" 
                                       name="data_empacotamento" 
                                       value="{{ old('data_empacotamento', $empacotamento->data_empacotamento->format('Y-m-d\TH:i')) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm @error('data_empacotamento') border-red-500 @enderror"
                                       required>
                                @error('data_empacotamento')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Atual (só visualização) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Atual
                                </label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium"
                                          style="background-color: {{ $empacotamento->status->cor }}20; color: {{ $empacotamento->status->cor }};">
                                        <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $empacotamento->status->cor }};"></div>
                                        {{ $empacotamento->status->nome }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mt-6">
                            <label for="observacoes_empacotamento" class="block text-sm font-medium text-gray-700 mb-2">
                                Observações do Empacotamento
                            </label>
                            <textarea id="observacoes_empacotamento"
                                      name="observacoes_empacotamento"
                                      rows="4"
                                      placeholder="Observações sobre o empacotamento..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm @error('observacoes_empacotamento') border-red-500 @enderror">{{ old('observacoes_empacotamento', $empacotamento->observacoes_empacotamento) }}</textarea>
                            @error('observacoes_empacotamento')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Divisão de Peças por Tipo -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Divisão de Peças por Tipo
                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $empacotamento->pecasIndividuais->count() }} lotes
                            </span>
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Divida as peças em lotes menores. Cada lote terá seu próprio QR Code para rastreamento</p>
                    </div>

                        <!-- Resumo de Status dos Lotes -->
                        @php
                            $totalLotes = $empacotamento->pecasIndividuais->count();
                            $lotesProcessados = $empacotamento->pecasIndividuais->where('quantidade', '>', 0)->count();
                            $lotesPendentes = $totalLotes - $lotesProcessados;
                            
                            // Contar tipos da coleta que ainda não têm lotes
                            $tiposSemLotes = $empacotamento->coleta->pecas->whereNotIn('tipo_id', $empacotamento->pecasIndividuais->pluck('tipo_id'))->count();
                        @endphp
                        @if($totalLotes > 0)

                            <!-- Resumo dos Lotes com Barra de Progresso -->
                            <div class="mt-4 p-3 bg-gray-50 rounded border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-semibold text-gray-700">Progresso dos Lotes:</h4>
                                    <span class="text-xs text-gray-600">{{ $lotesProcessados }}/{{ $totalLotes }} concluídos</span>
                                </div>

                                <!-- Barra de Progresso -->
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                         style="width: {{ $totalLotes > 0 ? ($lotesProcessados / $totalLotes) * 100 : 0 }}%"
                                         id="barra-progresso"></div>
                                </div>

                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-lg font-bold text-gray-900" id="contador-total">{{ $totalLotes }}</div>
                                        <div class="text-xs text-gray-600">Total</div>
                                    </div>
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-lg font-bold text-green-600" id="contador-processados">{{ $lotesProcessados }}</div>
                                        <div class="text-xs text-gray-600">Processados</div>
                                    </div>
                                    <div class="bg-white p-2 rounded border">
                                        <div class="text-lg font-bold {{ $lotesPendentes > 0 ? 'text-orange-600' : 'text-gray-400' }}" id="contador-pendentes">{{ $lotesPendentes }}</div>
                                        <div class="text-xs text-gray-600">Pendentes</div>
                                    </div>
                                </div>

                                @if($lotesProcessados > 0)
                                    <div class="mt-2 text-xs text-green-600 text-center">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Alguns lotes já foram processados e possuem QR codes gerados
                                    </div>
                                @endif

                                @if($lotesPendentes == 0)
                                    <div class="mt-2 text-xs text-green-600 text-center font-medium">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Todos os lotes foram processados!
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <!-- Container dos Tipos de Peças Agrupadas -->
                    <div class="p-4">
                        @php
                            // Combinar peças da coleta com peças já empacotadas
                            $pecasIndividuais = $empacotamento->pecasIndividuais->groupBy('tipo_id');
                            $coletaPecas = $empacotamento->coleta->pecas->keyBy('tipo_id');
                            
                            // Criar lista combinada de tipos (da coleta + empacotadas)
                            $todosTipos = collect();
                            $tiposEmTransito = collect();
                            
                            // Adicionar tipos da coleta
                            foreach ($coletaPecas as $tipoId => $coletaPeca) {
                                $pecasEmpacotadasTipo = $pecasIndividuais->get($tipoId, collect());
                                
                                // Verificar se todas as peças deste tipo estão em trânsito
                                $pecasDisponiveis = $pecasEmpacotadasTipo->where('status_saida', '!=', 'em_transito');
                                $pecasEmTransito = $pecasEmpacotadasTipo->where('status_saida', 'em_transito');
                                
                                $dados = [
                                    'tipo_id' => $tipoId,
                                    'tipo' => $coletaPeca->tipo,
                                    'quantidade_coletada' => $coletaPeca->quantidade,
                                    'pecas_empacotadas' => $pecasEmpacotadasTipo,
                                    'tem_pecas_em_transito' => $pecasEmTransito->count() > 0,
                                    'todas_em_transito' => $pecasDisponiveis->count() === 0 && $pecasEmTransito->count() > 0
                                ];
                                
                                if ($dados['todas_em_transito']) {
                                    $tiposEmTransito->put($tipoId, $dados);
                                } else {
                                    $todosTipos->put($tipoId, $dados);
                                }
                            }
                            
                            // Adicionar tipos que só existem no empacotamento (peças extras)
                            foreach ($pecasIndividuais as $tipoId => $pecasEmpacotadasTipo) {
                                if (!$todosTipos->has($tipoId) && !$tiposEmTransito->has($tipoId)) {
                                    $primeiraP = $pecasEmpacotadasTipo->first();
                                    
                                    $pecasDisponiveis = $pecasEmpacotadasTipo->where('status_saida', '!=', 'em_transito');
                                    $pecasEmTransito = $pecasEmpacotadasTipo->where('status_saida', 'em_transito');
                                    
                                    $dados = [
                                        'tipo_id' => $tipoId,
                                        'tipo' => $primeiraP->tipo,
                                        'quantidade_coletada' => 0,
                                        'pecas_empacotadas' => $pecasEmpacotadasTipo,
                                        'tem_pecas_em_transito' => $pecasEmTransito->count() > 0,
                                        'todas_em_transito' => $pecasDisponiveis->count() === 0 && $pecasEmTransito->count() > 0
                                    ];
                                    
                                    if ($dados['todas_em_transito']) {
                                        $tiposEmTransito->put($tipoId, $dados);
                                    } else {
                                        $todosTipos->put($tipoId, $dados);
                                    }
                                }
                            }
                        @endphp

                        @foreach($todosTipos as $tipoId => $dadosTipo)
                            @php
                                $tipo = $dadosTipo['tipo'];
                                $quantidadeColetada = $dadosTipo['quantidade_coletada'];
                                $pecasEmpacotadas = $dadosTipo['pecas_empacotadas'];
                                
                                $totalEmpacotado = $pecasEmpacotadas->sum('quantidade');
                                $diferenca = $totalEmpacotado - $quantidadeColetada;
                                
                                // Determinar se há lotes pendentes (sem quantidade)
                                $lotesPendentesTipo = $pecasEmpacotadas->where('quantidade', '=', 0)->count();
                            @endphp

                            <div class="tipo-peca-container border border-gray-200 rounded-lg overflow-hidden mb-4">
                                <!-- Barra/Título do Tipo -->
                                <div class="tipo-peca-header bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 p-4 cursor-pointer hover:from-blue-100 hover:to-indigo-100 transition-colors"
                                     onclick="toggleTipoEdicao('{{ $tipoId }}')"
                                     data-quantidade-coletada="{{ $quantidadeColetada }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <!-- Checkbox para seleção do tipo (visível apenas no modo selecionados) -->
                                            <div class="checkbox-selecao-tipo hidden">
                                                <label for="tipo_{{ $tipoId }}" class="flex items-center cursor-pointer" onclick="event.stopPropagation()">
                                                    <input type="checkbox"
                                                           name="tipos_selecionados[]"
                                                           value="{{ $tipoId }}"
                                                           id="tipo_{{ $tipoId }}"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                                    <span class="ml-1 text-xs text-gray-600">Selecionar</span>
                                                </label>
                                            </div>
                                            <div class="w-3 h-3 bg-blue-500 rounded-full indicador-tipo"></div>
                                            <div>
                                                <div class="flex items-center">
                                                    <h5 class="text-sm font-semibold text-gray-900">{{ $tipo->nome }}</h5>
                                                    @if($lotesPendentesTipo > 0)
                                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                                <circle cx="4" cy="4" r="3"/>
                                                            </svg>
                                                            {{ $lotesPendentesTipo }} pendente{{ $lotesPendentesTipo > 1 ? 's' : '' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500">{{ $tipo->categoria }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="text-right">
                                                <div class="text-xs text-gray-500">Coletado</div>
                                                <div class="text-sm font-medium text-gray-900">{{ $quantidadeColetada }} peças</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-gray-500">Empacotado</div>
                                                <div class="text-sm font-medium {{ $diferenca == 0 ? 'text-green-600' : ($diferenca > 0 ? 'text-orange-600' : 'text-red-600') }}">
                                                    {{ $totalEmpacotado }} peças
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-gray-500">Status</div>
                                                <div class="text-sm font-medium {{ $diferenca == 0 ? 'text-green-600' : ($diferenca > 0 ? 'text-orange-600' : 'text-red-600') }}">
                                                    @if($diferenca == 0)
                                                        ✓ Confere
                                                    @elseif($diferenca > 0)
                                                        +{{ $diferenca }} a mais
                                                    @else
                                                        {{ abs($diferenca) }} faltando
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-gray-500">Lotes</div>
                                                <div class="text-sm font-medium text-blue-600">
                                                    {{ $pecasEmpacotadas->count() }} lotes
                                                    @php
                                                        $lotesProcessados = $pecasEmpacotadas->where('quantidade', '>', 0)->count();
                                                    @endphp
                                                    @if($lotesProcessados > 0)
                                                        <div class="text-xs text-green-600 font-medium">
                                                            {{ $lotesProcessados }} processado{{ $lotesProcessados > 1 ? 's' : '' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($quantidadeColetada > 0 && $totalEmpacotado == 0)
                                                <button type="button"
                                                        onclick="event.stopPropagation(); criarLoteInicial('{{ $tipoId }}', '{{ $tipo->nome }}')"
                                                        class="mr-2 inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded transition-colors"
                                                        title="Criar lote inicial para este tipo">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Criar Lote
                                                </button>
                                            @elseif($lotesPendentesTipo > 0)
                                                <button type="button"
                                                        onclick="event.stopPropagation(); preencherLotesTipo('{{ $tipoId }}', {{ $lotesPendentesTipo }})"
                                                        class="mr-2 inline-flex items-center px-2 py-1 bg-orange-100 hover:bg-orange-200 text-orange-700 text-xs font-medium rounded transition-colors"
                                                        title="Preencher lotes pendentes deste tipo com quantidade 1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Preencher
                                                </button>
                                            @endif
                                            <svg id="chevron-{{ $tipoId }}" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Área de Conteúdo (Lotes) -->
                                <div id="content-{{ $tipoId }}" class="tipo-peca-content hidden bg-white">
                                    <div class="p-4">
                                        <div class="mb-4">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-gray-700">Lotes de empacotamento:</span>
                                                                                                    @php
                                                        $lotesProcessados = $pecasEmpacotadas->where('quantidade', '>', 0)->count();
                                                        $totalLotes = $pecasEmpacotadas->count();
                                                    @endphp
                                                @if($lotesProcessados > 0)
                                                    <div class="flex items-center text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $lotesProcessados }} de {{ $totalLotes }} lotes já processados
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            @if($pecasEmpacotadas->count() > 0)
                                                @foreach($pecasEmpacotadas as $index => $peca)
                                                <div class="lote-empacotamento flex items-center space-x-3 p-3 rounded border {{ $peca->quantidade > 0 ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                                    <!-- Badge de Status -->
                                                    @if($peca->quantidade > 0)
                                                        <div class="flex-shrink-0">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Processado
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="flex-shrink-0">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Pendente
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <div class="flex-1 grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Código QR</label>
                                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium {{ $peca->quantidade > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                                {{ $peca->codigo_qr }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                                Quantidade
                                                                @if($peca->quantidade > 0)
                                                                    <span class="ml-1 text-xs text-green-600 font-medium">(já processado)</span>
                                                                @endif
                                                            </label>
                                                            <input type="number"
                                                                   name="pecas_individuais[{{ $peca->id }}][quantidade]"
                                                                   value="{{ $peca->quantidade }}"
                                                                   min="0"
                                                                   data-tipo-id="{{ $tipoId }}"
                                                                   data-quantidade-original="{{ $peca->quantidade }}"
                                                                   onchange="atualizarStatusTipo('{{ $tipoId }}')"
                                                                   oninput="atualizarStatusTipo('{{ $tipoId }}')"
                                                                   class="quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 {{ $peca->quantidade > 0 ? 'bg-green-50 border-green-300 font-semibold text-green-800' : '' }}">
                                                            <!-- Campo hidden para peso (mantém valor original) -->
                                                            <input type="hidden" name="pecas_individuais[{{ $peca->id }}][peso]" value="{{ $peca->peso }}">

                                                            @if($peca->quantidade > 0)
                                                                <div class="mt-1 text-xs text-green-600">
                                                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Este lote já foi empacotado com {{ $peca->quantidade }} peça{{ $peca->quantidade > 1 ? 's' : '' }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($pecasEmpacotadas->count() > 1)
                                                        <div class="flex-shrink-0">
                                                            <button type="button" onclick="removerLoteEdicao({{ $peca->id }})"
                                                                    class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                                                                    title="Remover lote">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                                @endforeach
                                            @else
                                                <!-- Mostrar mensagem quando não há lotes criados ainda -->
                                                <div class="text-center py-8 text-gray-500">
                                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-6a2 2 0 00-2 2v1a2 2 0 01-2 2H8a2 2 0 01-2-2v-1a2 2 0 00-2-2H2"></path>
                                                    </svg>
                                                    <p class="text-sm">Nenhum lote criado ainda para este tipo</p>
                                                    <p class="text-xs mt-1">Clique em "Criar Lote" para começar</p>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Botão Adicionar Lote no final -->
                                        <div class="mt-4 pt-3 border-t border-gray-200">
                                            <button type="button" onclick="duplicarLoteEdicao('{{ $tipoId }}', '{{ $tipo->nome }}')"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Adicionar Novo Lote
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Seção de Tipos em Trânsito -->
                        @if($tiposEmTransito->count() > 0)
                            <div class="mt-6 border-t border-gray-300 pt-4">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Peças em Trânsito
                                    <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $tiposEmTransito->count() }} tipos
                                    </span>
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">🚚 Estas peças já saíram com o motorista</p>
                                
                                @foreach($tiposEmTransito as $tipoId => $dadosTipo)
                                    @php
                                        $tipo = $dadosTipo['tipo'];
                                        $quantidadeColetada = $dadosTipo['quantidade_coletada'];
                                        $pecasEmpacotadas = $dadosTipo['pecas_empacotadas'];
                                        $totalEmpacotado = $pecasEmpacotadas->sum('quantidade');
                                        $pecasEmTransito = $pecasEmpacotadas->where('status_saida', 'em_transito');
                                    @endphp
                                    
                                    <div class="tipo-peca-container border border-green-200 rounded-lg overflow-hidden mb-4 bg-green-50">
                                        <!-- Cabeçalho do Tipo em Trânsito -->
                                        <div class="bg-gradient-to-r from-green-100 to-emerald-100 border-b border-green-200 p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <h4 class="text-base font-semibold text-gray-800">{{ $tipo->nome }}</h4>
                                                    <span class="ml-2 text-xs text-gray-600 bg-white px-2 py-1 rounded">{{ $tipo->categoria }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs text-green-600 font-semibold bg-green-200 px-2 py-1 rounded">
                                                        🚚 Em Trânsito
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-3 gap-4 mt-3 text-sm">
                                                <div class="text-center">
                                                    <div class="text-xs text-gray-600">Coletado</div>
                                                    <div class="font-semibold">{{ $quantidadeColetada }} peças</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-xs text-gray-600">Empacotado</div>
                                                    <div class="font-semibold">{{ $totalEmpacotado }} peças</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-xs text-gray-600">Status</div>
                                                    <div class="font-semibold text-green-600">✓ Em Trânsito</div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3 text-xs text-gray-700">
                                                📦 <strong>{{ $pecasEmTransito->count() }} sacola(s)</strong> confirmada(s) para entrega
                                                @if($pecasEmTransito->first() && $pecasEmTransito->first()->data_saida)
                                                    • Saída: {{ $pecasEmTransito->first()->data_saida->format('d/m/Y H:i') }}
                                                @endif
                                            </div>
                                            
                                            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                                                ⚠️ <strong>Importante:</strong> Se você editar este tipo, será necessário gerar novos QR codes para impressão
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Botão Adicionar Peça Extra -->
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <button type="button" onclick="abrirModalPecaExtra()"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Peça Extra (Novo Tipo)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar com informações da coleta -->
            <div class="lg:col-span-1">
                <!-- Informações da Coleta -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informações da Coleta
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Número:</span>
                                <span class="text-sm font-medium text-gray-900 ml-2">{{ $empacotamento->coleta->numero_coleta }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Estabelecimento:</span>
                                <span class="text-sm font-medium text-gray-900 ml-2">{{ $empacotamento->coleta->estabelecimento->razao_social }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Peso Total:</span>
                                <span class="text-sm font-medium text-gray-900 ml-2">{{ number_format($empacotamento->coleta->peso_total, 2, ',', '.') }} kg</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Responsável:</span>
                                <span class="text-sm font-medium text-gray-900 ml-2">{{ $empacotamento->usuarioEmpacotamento->nome }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aviso sobre edição -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-800 mb-1">Atenção</h4>
                            <p class="text-sm text-yellow-700">
                                Alterações nas quantidades empacotadas afetarão os cálculos de valor e peso total do empacotamento. 
                                Certifique-se de que os dados estão corretos antes de salvar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end mt-6">
            <a href="{{ route('empacotamento.show', $empacotamento->id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-xl transition-colors duration-200">
                Cancelar
            </a>
            <button type="submit" onclick="return validarFormulario()"
                    class="inline-flex items-center justify-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<!-- Modal Adicionar Peça Extra -->
<div id="modalPecaExtra" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Adicionar Peça Extra</h3>
                <button type="button" onclick="fecharModalPecaExtra()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="formPecaExtra">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Peça</label>
                    <select id="tipoExtraSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Selecione um tipo...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" data-nome="{{ $tipo->nome }}" data-categoria="{{ $tipo->categoria }}">
                                {{ $tipo->nome }} ({{ $tipo->categoria }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                    <input type="number" id="quantidadeExtra" min="1" value="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea id="observacoesExtra" rows="3" placeholder="Ex: Peça encontrada durante empacotamento..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="fecharModalPecaExtra()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-md transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="adicionarPecaExtra()"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition-colors">
                        Adicionar Peça
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para alterar modo de empacotamento
    window.alterarModoEmpacotamento = function() {
        const modoSelecionado = document.querySelector('input[name="modo_empacotamento"]:checked').value;
        const checkboxes = document.querySelectorAll('.checkbox-selecao-tipo');
        const instrucoes = document.getElementById('instrucoes-modo');
        const contador = document.getElementById('contador-selecionados');
        const acoes = document.getElementById('acoes-selecao');

        if (modoSelecionado === 'selecionados') {
            // Mostrar checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.classList.remove('hidden');
                // Adicionar listener para destacar tipos selecionados
                const input = checkbox.querySelector('input[type="checkbox"]');
                if (input) {
                    input.addEventListener('change', destacarTipoSelecionado);
                }
            });
            instrucoes.textContent = 'Selecione os tipos de peças que deseja empacotar. Apenas os selecionados receberão QR codes.';
            contador.classList.remove('hidden');
            acoes.classList.remove('hidden');
            atualizarContadorSelecionados();
        } else {
            // Esconder checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.classList.add('hidden');
                // Desmarcar todos os checkboxes
                const input = checkbox.querySelector('input[type="checkbox"]');
                if (input) {
                    input.checked = false;
                    input.removeEventListener('change', destacarTipoSelecionado);
                }
                // Remover destaque
                const container = checkbox.closest('.tipo-peca-container');
                if (container) {
                    container.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
                }
            });
            instrucoes.textContent = 'Todos os lotes serão processados e receberão QR codes.';
            contador.classList.add('hidden');
            acoes.classList.add('hidden');
        }
    };

    // Função para validar formulário antes do envio
    window.validarFormulario = function() {
        const modoSelecionado = document.querySelector('input[name="modo_empacotamento"]:checked').value;

        if (modoSelecionado === 'selecionados') {
            const tiposSelecionados = document.querySelectorAll('input[name="tipos_selecionados[]"]:checked');

            if (tiposSelecionados.length === 0) {
                alert('Por favor, selecione pelo menos um tipo de peça para empacotar.');
                return false;
            }

            // Verificar se os tipos selecionados têm quantidades válidas
            let temQuantidadeValida = false;
            tiposSelecionados.forEach(checkbox => {
                const tipoId = checkbox.value;
                const inputs = document.querySelectorAll(`input[data-tipo-id="${tipoId}"]`);
                inputs.forEach(input => {
                    if (parseInt(input.value) > 0) {
                        temQuantidadeValida = true;
                    }
                });
            });

            if (!temQuantidadeValida) {
                alert('Os tipos selecionados devem ter pelo menos uma quantidade maior que zero.');
                return false;
            }
        }

        // Verificar se há lotes pendentes e alertar o usuário
        const lotesPendentes = [];
        document.querySelectorAll('.lote-empacotamento input[type="number"]').forEach(input => {
            if ((parseInt(input.value) || 0) === 0) {
                lotesPendentes.push(input);
            }
        });

        if (lotesPendentes.length > 0) {
            const resposta = confirm(
                `Atenção: Você tem ${lotesPendentes.length} lote${lotesPendentes.length > 1 ? 's' : ''} pendente${lotesPendentes.length > 1 ? 's' : ''} (sem quantidade preenchida).\n\n` +
                `Estes lotes não receberão QR codes e permanecerão disponíveis para processamento posterior.\n\n` +
                `Deseja continuar mesmo assim?\n\n` +
                `• Clique "OK" para salvar apenas os lotes preenchidos\n` +
                `• Clique "Cancelar" para voltar e preencher os lotes pendentes`
            );

            if (!resposta) {
                // Destacar lotes pendentes para ajudar o usuário
                destacarLotesPendentes();
                return false;
            }
        }

        return true;
    };

    // Função para destacar tipos selecionados
    window.destacarTipoSelecionado = function(event) {
        const checkbox = event.target;
        const container = checkbox.closest('.tipo-peca-container');

        if (container) {
            if (checkbox.checked) {
                container.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
            } else {
                container.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
            }
        }
        atualizarContadorSelecionados();
    };

    // Função para atualizar contador de tipos selecionados
    window.atualizarContadorSelecionados = function() {
        const tiposSelecionados = document.querySelectorAll('input[name="tipos_selecionados[]"]:checked');
        const contador = document.getElementById('contador-selecionados');
        const quantidade = tiposSelecionados.length;

        if (contador) {
            contador.textContent = `${quantidade} tipo${quantidade !== 1 ? 's' : ''} selecionado${quantidade !== 1 ? 's' : ''}`;

            // Mudar cor baseado na quantidade
            contador.className = 'mt-1 text-xs font-medium ' +
                (quantidade > 0 ? 'text-green-600' : 'text-blue-600');
        }
    };

    // Função para selecionar todos os tipos
    window.selecionarTodosTipos = function() {
        const checkboxes = document.querySelectorAll('input[name="tipos_selecionados[]"]');
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.checked = true;
                destacarTipoSelecionado({ target: checkbox });
            }
        });
    };

    // Função para desselecionar todos os tipos
    window.deselecionarTodosTipos = function() {
        const checkboxes = document.querySelectorAll('input[name="tipos_selecionados[]"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
                destacarTipoSelecionado({ target: checkbox });
            }
        });
    };

    // Função para toggle do tipo de peça (expandir/recolher)
    window.toggleTipoEdicao = function(tipoId) {
        const content = document.getElementById(`content-${tipoId}`);
        const chevron = document.getElementById(`chevron-${tipoId}`);

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
        }
    };

    // Função para criar lote inicial (primeiro lote de um tipo)
    window.criarLoteInicial = function(tipoId, tipoNome) {
        const container = document.querySelector(`#content-${tipoId} .space-y-3`);
        const novoIndex = Date.now();

        const novoLote = document.createElement('div');
        novoLote.className = 'lote-empacotamento flex items-center space-x-3 p-3 bg-gray-50 rounded border border-gray-200';
        novoLote.innerHTML = `
            <div class="flex-shrink-0">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pendente
                </span>
            </div>
            <div class="flex-1 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Código QR</label>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-gray-100 text-gray-800">
                        Será gerado
                    </span>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade</label>
                    <input type="number"
                           name="novos_lotes[${novoIndex}][quantidade]"
                           value="0"
                           min="0"
                           data-tipo-id="${tipoId}"
                           onchange="atualizarStatusTipo('${tipoId}')"
                           oninput="atualizarStatusTipo('${tipoId}')"
                           class="quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <input type="hidden" name="novos_lotes[${novoIndex}][tipo_id]" value="${tipoId}">
                    <input type="hidden" name="novos_lotes[${novoIndex}][tipo_nome]" value="${tipoNome}">
                    <input type="hidden" name="novos_lotes[${novoIndex}][peso]" value="0">
                </div>
            </div>
            <div class="flex-shrink-0">
                <button type="button" onclick="removerNovoLote(this, '${tipoId}')"
                        class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                        title="Remover lote">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;

        // Remover mensagem de "nenhum lote criado" se existir
        const mensagemVazia = container.querySelector('.text-center.py-8');
        if (mensagemVazia) {
            mensagemVazia.remove();
        }

        container.appendChild(novoLote);

        // Atualizar status após adicionar
        atualizarStatusTipo(tipoId);

        // Scroll suave para o novo lote
        setTimeout(() => {
            novoLote.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Focar no input de quantidade
            const input = novoLote.querySelector('.quantidade-lote');
            if (input) {
                input.focus();
                input.select();
            }
        }, 100);
    };

    // Função para duplicar lote (adicionar novo lote do mesmo tipo)
    window.duplicarLoteEdicao = function(tipoId, tipoNome) {
        const container = document.querySelector(`#content-${tipoId} .space-y-3`);
        const novoIndex = Date.now();

        const novoLote = document.createElement('div');
        novoLote.className = 'lote-empacotamento flex items-center space-x-3 p-3 bg-gray-50 rounded border';
        novoLote.innerHTML = `
            <div class="flex-1 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Código QR</label>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-yellow-100 text-yellow-800">
                        Será gerado
                    </span>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade</label>
                    <input type="number"
                           name="novos_lotes[${novoIndex}][quantidade]"
                           value="0"
                           min="0"
                           data-tipo-id="${tipoId}"
                           onchange="atualizarStatusTipo('${tipoId}')"
                           oninput="atualizarStatusTipo('${tipoId}')"
                           class="quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <input type="hidden" name="novos_lotes[${novoIndex}][tipo_id]" value="${tipoId}">
                    <input type="hidden" name="novos_lotes[${novoIndex}][tipo_nome]" value="${tipoNome}">
                    <input type="hidden" name="novos_lotes[${novoIndex}][peso]" value="0">
                </div>
            </div>
            <div class="flex-shrink-0">
                <button type="button" onclick="removerNovoLote(this, '${tipoId}')"
                        class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                        title="Remover lote">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;

        container.appendChild(novoLote);

        // Atualizar status após adicionar
        atualizarStatusTipo(tipoId);

        // Scroll suave para o novo lote
        setTimeout(() => {
            novoLote.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Focar no input de quantidade
            const input = novoLote.querySelector('.quantidade-lote');
            if (input) {
                input.focus();
                input.select();
            }
        }, 100);
    };

    // Função para remover lote existente
    window.removerLoteEdicao = function(pecaId) {
        if (confirm('Tem certeza que deseja remover este lote? Esta ação não pode ser desfeita.')) {
            // Adicionar campo hidden para marcar como removido
            const form = document.querySelector('form');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'lotes_removidos[]';
            hiddenInput.value = pecaId;
            form.appendChild(hiddenInput);

            // Obter tipo antes de remover
            const lote = document.querySelector(`input[name="pecas_individuais[${pecaId}][quantidade]"]`).closest('.lote-empacotamento');
            const tipoContainer = lote.closest('.tipo-peca-container');
            const tipoId = tipoContainer.querySelector('.tipo-peca-header').onclick.toString().match(/'(\d+)'/)[1];

            // Remover visualmente
            lote.remove();

            // Atualizar status
            atualizarStatusTipo(tipoId);
        }
    };

    // Função para remover novo lote
    window.removerNovoLote = function(botao, tipoId) {
        botao.closest('.lote-empacotamento').remove();
        atualizarStatusTipo(tipoId);
    };

    // Função para atualizar status do tipo em tempo real
    window.atualizarStatusTipo = function(tipoId) {
        const tipoContainer = document.querySelector(`[onclick*="'${tipoId}'"]`).closest('.tipo-peca-container');

        // Buscar quantidade coletada (do atributo data)
        const header = tipoContainer.querySelector('.tipo-peca-header');
        const quantidadeColetada = parseInt(header.dataset.quantidadeColetada) || 0;

        // Calcular total empacotado (lotes existentes + novos lotes)
        let totalEmpacotado = 0;
        let totalLotes = 0;

        // Lotes existentes
        const lotesExistentes = tipoContainer.querySelectorAll('input[name*="pecas_individuais"]');
        lotesExistentes.forEach(input => {
            if (input.name.includes('[quantidade]')) {
                totalEmpacotado += parseInt(input.value) || 0;
                totalLotes++;
            }
        });

        // Novos lotes
        const novosLotes = tipoContainer.querySelectorAll('input[name*="novos_lotes"]');
        novosLotes.forEach(input => {
            if (input.name.includes('[quantidade]')) {
                totalEmpacotado += parseInt(input.value) || 0;
                totalLotes++;
            }
        });

        // Calcular diferença
        const diferenca = totalEmpacotado - quantidadeColetada;

        // Atualizar displays na barra (buscar de forma mais específica)
        const headerDiv = tipoContainer.querySelector('.tipo-peca-header');
        const displays = headerDiv.querySelectorAll('.text-right .text-sm.font-medium');

        if (displays.length < 4) {
            console.error('Não foi possível encontrar todos os displays necessários');
            return;
        }

        const empacotadoDisplay = displays[1]; // Segundo display (Empacotado)
        const statusDisplay = displays[2];     // Terceiro display (Status)
        const lotesDisplay = displays[3];      // Quarto display (Lotes)

        // Atualizar quantidade empacotada
        empacotadoDisplay.textContent = `${totalEmpacotado} peças`;

        // Atualizar status e cores
        if (diferenca === 0) {
            statusDisplay.textContent = '✓ Confere';
            statusDisplay.className = 'text-sm font-medium text-green-600';
            empacotadoDisplay.className = 'text-sm font-medium text-green-600';
        } else if (diferenca > 0) {
            statusDisplay.textContent = `+${diferenca} a mais`;
            statusDisplay.className = 'text-sm font-medium text-orange-600';
            empacotadoDisplay.className = 'text-sm font-medium text-orange-600';
        } else {
            statusDisplay.textContent = `${Math.abs(diferenca)} faltando`;
            statusDisplay.className = 'text-sm font-medium text-red-600';
            empacotadoDisplay.className = 'text-sm font-medium text-red-600';
        }

        // Atualizar número de lotes
        lotesDisplay.textContent = `${totalLotes} lotes`;

        // Atualizar status visual dos lotes individuais
        atualizarStatusLotesIndividuais(tipoId);

        // Atualizar contadores globais
        atualizarContadoresGlobais();
    };

    // Função para atualizar status visual dos lotes individuais
    window.atualizarStatusLotesIndividuais = function(tipoId) {
        const tipoContainer = document.querySelector(`[onclick*="'${tipoId}'"]`).closest('.tipo-peca-container');
        const lotes = tipoContainer.querySelectorAll('.lote-empacotamento');

        lotes.forEach(lote => {
            const input = lote.querySelector('input[type="number"]');
            if (!input) return;

            const quantidade = parseInt(input.value) || 0;
            const quantidadeOriginal = parseInt(input.dataset.quantidadeOriginal) || 0;

            // Atualizar badge de status
            const badge = lote.querySelector('.inline-flex.items-center.px-2.py-1.rounded-full');
            const qrCode = lote.querySelector('.inline-flex.items-center.px-2.py-1.rounded:not(.rounded-full)');

            if (quantidade > 0) {
                // Lote processado
                lote.className = 'lote-empacotamento flex items-center space-x-3 p-3 rounded border bg-green-50 border-green-200';

                if (badge) {
                    badge.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                    badge.innerHTML = `
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Processado
                    `;
                }

                if (qrCode) {
                    qrCode.className = 'inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-green-100 text-green-800';
                }

                // Atualizar classe do input
                input.className = 'quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-green-50 border-green-300 font-semibold text-green-800';

            } else {
                // Lote pendente
                lote.className = 'lote-empacotamento flex items-center space-x-3 p-3 rounded border bg-gray-50 border-gray-200';

                if (badge) {
                    badge.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
                    badge.innerHTML = `
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pendente
                    `;
                }

                if (qrCode) {
                    qrCode.className = 'inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-gray-100 text-gray-800';
                }

                // Atualizar classe do input
                input.className = 'quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500';
            }

            // Atualizar/criar aviso de lote processado
            const avisoExistente = lote.querySelector('.mt-1.text-xs.text-green-600');
            const labelQuantidade = lote.querySelector('label');

            if (quantidade > 0) {
                // Atualizar label
                if (labelQuantidade && !labelQuantidade.innerHTML.includes('(já processado)')) {
                    labelQuantidade.innerHTML = `
                        Quantidade
                        <span class="ml-1 text-xs text-green-600 font-medium">(já processado)</span>
                    `;
                }

                // Criar/atualizar aviso se não existir
                if (!avisoExistente) {
                    const avisoDiv = document.createElement('div');
                    avisoDiv.className = 'mt-1 text-xs text-green-600';
                    avisoDiv.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Este lote já foi empacotado com ${quantidade} peça${quantidade > 1 ? 's' : ''}
                    `;
                    input.parentNode.appendChild(avisoDiv);
                } else {
                    avisoExistente.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Este lote já foi empacotado com ${quantidade} peça${quantidade > 1 ? 's' : ''}
                    `;
                }
            } else {
                // Remover aviso se quantidade for 0
                if (avisoExistente) {
                    avisoExistente.remove();
                }

                // Restaurar label original
                if (labelQuantidade && labelQuantidade.innerHTML.includes('(já processado)')) {
                    labelQuantidade.innerHTML = 'Quantidade';
                }
            }
        });
    };

    // Função para destacar lotes pendentes
    window.destacarLotesPendentes = function() {
        // Remover destaque anterior
        document.querySelectorAll('.lote-empacotamento').forEach(lote => {
            lote.classList.remove('ring-4', 'ring-yellow-400', 'ring-opacity-75');
        });

        // Encontrar e destacar lotes pendentes
        const lotesPendentes = [];
        document.querySelectorAll('.lote-empacotamento').forEach(lote => {
            const input = lote.querySelector('input[type="number"]');
            if (input && (parseInt(input.value) || 0) === 0) {
                lote.classList.add('ring-4', 'ring-yellow-400', 'ring-opacity-75');
                lotesPendentes.push(lote);
            }
        });

        if (lotesPendentes.length > 0) {
            // Expandir tipos que contêm lotes pendentes
            lotesPendentes.forEach(lote => {
                const tipoContainer = lote.closest('.tipo-peca-container');
                const content = tipoContainer.querySelector('.tipo-peca-content');
                const chevron = tipoContainer.querySelector('[id^="chevron-"]');

                if (content && content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
            });

            // Scroll para o primeiro lote pendente
            setTimeout(() => {
                lotesPendentes[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 300);

            // Remover destaque após 5 segundos
            setTimeout(() => {
                lotesPendentes.forEach(lote => {
                    lote.classList.remove('ring-4', 'ring-yellow-400', 'ring-opacity-75');
                });
            }, 5000);
        }
    };

    // Função para preencher todos os lotes pendentes com 1
    window.preencherTodosComUm = function() {
        if (!confirm('Deseja preencher todos os lotes pendentes com quantidade 1?')) {
            return;
        }

        let lotesPreenchidos = 0;
        document.querySelectorAll('.lote-empacotamento').forEach(lote => {
            const input = lote.querySelector('input[type="number"]');
            if (input && (parseInt(input.value) || 0) === 0) {
                input.value = 1;
                // Disparar evento de mudança para atualizar status
                const tipoId = input.dataset.tipoId;
                if (tipoId) {
                    atualizarStatusTipo(tipoId);
                }
                lotesPreenchidos++;
            }
        });

        if (lotesPreenchidos > 0) {
            // Atualizar contadores globais
            atualizarContadoresGlobais();

            // Mostrar mensagem de sucesso
            mostrarMensagemTemporaria(`${lotesPreenchidos} lote${lotesPreenchidos > 1 ? 's' : ''} preenchido${lotesPreenchidos > 1 ? 's' : ''} com quantidade 1`, 'success');
        }
    };

    // Função para preencher lotes pendentes de um tipo específico
    window.preencherLotesTipo = function(tipoId, quantidadePendentes) {
        if (!confirm(`Deseja preencher os ${quantidadePendentes} lote${quantidadePendentes > 1 ? 's' : ''} pendente${quantidadePendentes > 1 ? 's' : ''} deste tipo com quantidade 1?`)) {
            return;
        }

        let lotesPreenchidos = 0;
        const tipoContainer = document.querySelector(`[onclick*="'${tipoId}'"]`).closest('.tipo-peca-container');

        if (tipoContainer) {
            const inputs = tipoContainer.querySelectorAll('.lote-empacotamento input[type="number"]');
            inputs.forEach(input => {
                if ((parseInt(input.value) || 0) === 0) {
                    input.value = 1;
                    lotesPreenchidos++;
                }
            });

            // Atualizar status do tipo
            atualizarStatusTipo(tipoId);

            if (lotesPreenchidos > 0) {
                mostrarMensagemTemporaria(`${lotesPreenchidos} lote${lotesPreenchidos > 1 ? 's' : ''} preenchido${lotesPreenchidos > 1 ? 's' : ''} neste tipo`, 'success');
            }
        }
    };

    // Função para fechar alerta de pendentes
    window.fecharAlertaPendentes = function() {
        const alerta = document.getElementById('alerta-lotes-pendentes');
        if (alerta) {
            alerta.style.display = 'none';
        }
    };

    // Função para atualizar contadores globais
    window.atualizarContadoresGlobais = function() {
        let totalLotes = 0;
        let lotesProcessados = 0;

        document.querySelectorAll('.lote-empacotamento input[type="number"]').forEach(input => {
            totalLotes++;
            if (parseInt(input.value) > 0) {
                lotesProcessados++;
            }
        });

        const lotesPendentes = totalLotes - lotesProcessados;

        // Atualizar contadores
        const contadorTotal = document.getElementById('contador-total');
        const contadorProcessados = document.getElementById('contador-processados');
        const contadorPendentes = document.getElementById('contador-pendentes');
        const barraProgresso = document.getElementById('barra-progresso');

        if (contadorTotal) contadorTotal.textContent = totalLotes;
        if (contadorProcessados) contadorProcessados.textContent = lotesProcessados;
        if (contadorPendentes) {
            contadorPendentes.textContent = lotesPendentes;
            contadorPendentes.className = `text-lg font-bold ${lotesPendentes > 0 ? 'text-orange-600' : 'text-gray-400'}`;
        }

        // Atualizar barra de progresso
        if (barraProgresso) {
            const porcentagem = totalLotes > 0 ? (lotesProcessados / totalLotes) * 100 : 0;
            barraProgresso.style.width = `${porcentagem}%`;
        }

        // Mostrar/esconder alerta de pendentes
        const alertaPendentes = document.getElementById('alerta-lotes-pendentes');
        if (alertaPendentes) {
            if (lotesPendentes > 0) {
                alertaPendentes.style.display = 'block';
                // Atualizar texto do alerta
                const titulo = alertaPendentes.querySelector('h3');
                if (titulo) {
                    titulo.textContent = `Atenção: ${lotesPendentes} lote${lotesPendentes > 1 ? 's' : ''} pendente${lotesPendentes > 1 ? 's' : ''}`;
                }
            } else {
                alertaPendentes.style.display = 'none';
            }
        }
    };

    // Função para mostrar mensagem temporária
    window.mostrarMensagemTemporaria = function(mensagem, tipo = 'info') {
        const cores = {
            success: 'bg-green-100 border-green-200 text-green-800',
            warning: 'bg-yellow-100 border-yellow-200 text-yellow-800',
            error: 'bg-red-100 border-red-200 text-red-800',
            info: 'bg-blue-100 border-blue-200 text-blue-800'
        };

        const div = document.createElement('div');
        div.className = `fixed top-4 right-4 p-3 rounded border ${cores[tipo]} z-50 shadow-lg`;
        div.innerHTML = `
            <div class="flex items-center">
                <span class="text-sm font-medium">${mensagem}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-current opacity-70 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(div);

        // Remover após 3 segundos
        setTimeout(() => {
            if (div.parentElement) {
                div.remove();
            }
        }, 3000);
    };

    // Função para abrir modal de peça extra
    window.abrirModalPecaExtra = function() {
        document.getElementById('modalPecaExtra').classList.remove('hidden');
        document.getElementById('tipoExtraSelect').focus();
    };

    // Função para fechar modal de peça extra
    window.fecharModalPecaExtra = function() {
        document.getElementById('modalPecaExtra').classList.add('hidden');
        // Limpar campos
        document.getElementById('tipoExtraSelect').value = '';
        document.getElementById('quantidadeExtra').value = '1';
        document.getElementById('observacoesExtra').value = '';
    };

    // Função para adicionar peça extra
    window.adicionarPecaExtra = function() {
        const tipoSelect = document.getElementById('tipoExtraSelect');
        const quantidade = document.getElementById('quantidadeExtra').value;
        const observacoes = document.getElementById('observacoesExtra').value;

        console.log('Adicionando peça extra:', { tipoSelect: tipoSelect.value, quantidade, observacoes });

        // Validações
        if (!tipoSelect.value) {
            alert('Por favor, selecione um tipo de peça.');
            return;
        }

        if (!quantidade || quantidade < 1) {
            alert('Por favor, informe uma quantidade válida.');
            return;
        }

        const tipoId = tipoSelect.value;
        const tipoNome = tipoSelect.options[tipoSelect.selectedIndex].dataset.nome;
        const tipoCategoria = tipoSelect.options[tipoSelect.selectedIndex].dataset.categoria;

        console.log('Dados da peça extra:', { tipoId, tipoNome, tipoCategoria, quantidade, observacoes });

        // Verificar se já existe um container para este tipo
        // Buscar pelo container principal, não pelos inputs
        let tipoContainer = document.querySelector(`.tipo-peca-container[onclick*="'${tipoId}'"]`) ||
                           document.querySelector(`[onclick*="toggleTipoEdicao('${tipoId}')"]`)?.closest('.tipo-peca-container');

        console.log('Container existente encontrado:', tipoContainer);

        if (tipoContainer) {
            // Se já existe, adicionar um novo lote ao tipo existente
            const container = tipoContainer.querySelector('.space-y-3');
            console.log('Container de lotes encontrado:', container);

            if (!container) {
                console.error('Não foi possível encontrar o container de lotes dentro do tipo existente');
                return;
            }

            const novoIndex = Date.now();

            const novoLote = document.createElement('div');
            novoLote.className = 'lote-empacotamento flex items-center space-x-3 p-3 bg-purple-50 rounded border border-purple-200';
            novoLote.innerHTML = `
                <div class="flex-1 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Código QR</label>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-purple-100 text-purple-800">
                            Será gerado (EXTRA)
                        </span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade</label>
                        <input type="number"
                               name="pecas_extras[${novoIndex}][quantidade]"
                               value="${quantidade}"
                               min="0"
                               data-tipo-id="${tipoId}"
                               onchange="atualizarStatusTipo('${tipoId}')"
                               oninput="atualizarStatusTipo('${tipoId}')"
                               class="quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <input type="hidden" name="pecas_extras[${novoIndex}][tipo_id]" value="${tipoId}">
                        <input type="hidden" name="pecas_extras[${novoIndex}][tipo_nome]" value="${tipoNome}">
                        <input type="hidden" name="pecas_extras[${novoIndex}][observacoes]" value="Peça extra: ${observacoes}">
                        <input type="hidden" name="pecas_extras[${novoIndex}][peso]" value="0">
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" onclick="removerNovoLote(this, '${tipoId}')"
                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                            title="Remover peça extra">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;

            container.appendChild(novoLote);
            console.log('Novo lote adicionado ao tipo existente');
            atualizarStatusTipo(tipoId);

        } else {
            // Se não existe, criar um novo container para este tipo
            console.log('Tipo não existe, criando novo container');
            criarNovoTipoExtra(tipoId, tipoNome, tipoCategoria, quantidade, observacoes);
        }

        // Fechar modal
        fecharModalPecaExtra();

        console.log('Peça extra adicionada com sucesso!');

        // Scroll para o novo item
        setTimeout(() => {
            const ultimoLote = document.querySelector(`[onclick*="'${tipoId}'"]`)?.closest('.tipo-peca-container')?.querySelector('.lote-empacotamento:last-child');
            if (ultimoLote) {
                ultimoLote.scrollIntoView({ behavior: 'smooth', block: 'center' });
                console.log('Scroll realizado para o novo lote');
            } else {
                console.log('Não foi possível encontrar o lote para scroll');
            }
        }, 100);
    };

    // Função para criar novo tipo extra
    window.criarNovoTipoExtra = function(tipoId, tipoNome, tipoCategoria, quantidade, observacoes) {
        console.log('Criando novo tipo extra:', { tipoId, tipoNome, tipoCategoria, quantidade, observacoes });

        const novoIndex = Date.now();

        const novoTipoContainer = document.createElement('div');
        novoTipoContainer.className = 'tipo-peca-container border border-purple-200 rounded-lg overflow-hidden mb-4';
        novoTipoContainer.setAttribute('data-tipo-id', tipoId);

        novoTipoContainer.innerHTML = `
            <!-- Barra/Título do Tipo Extra -->
            <div class="tipo-peca-header bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-200 p-4 cursor-pointer hover:from-purple-100 hover:to-pink-100 transition-colors"
                 onclick="toggleTipoEdicao('${tipoId}')"
                 data-quantidade-coletada="0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900">${tipoNome} <span class="text-purple-600">(EXTRA)</span></h5>
                            <p class="text-xs text-gray-500">${tipoCategoria}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Coletado</div>
                            <div class="text-sm font-medium text-gray-900">0 peças</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Empacotado</div>
                            <div class="text-sm font-medium text-purple-600">${quantidade} peças</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Status</div>
                            <div class="text-sm font-medium text-orange-600">+${quantidade} a mais</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Lotes</div>
                            <div class="text-sm font-medium text-purple-600">1 lotes</div>
                        </div>
                        <svg id="chevron-${tipoId}" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Área de Conteúdo (Lotes) -->
            <div id="content-${tipoId}" class="tipo-peca-content hidden bg-white">
                <div class="p-4">
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-700">Lotes de empacotamento:</span>
                    </div>

                    <div class="space-y-3">
                        <div class="lote-empacotamento flex items-center space-x-3 p-3 bg-purple-50 rounded border border-purple-200">
                            <div class="flex-1 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Código QR</label>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-purple-100 text-purple-800">
                                        Será gerado (EXTRA)
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade</label>
                                    <input type="number"
                                           name="pecas_extras[${novoIndex}][quantidade]"
                                           value="${quantidade}"
                                           min="0"
                                           data-tipo-id="${tipoId}"
                                           onchange="atualizarStatusTipo('${tipoId}')"
                                           oninput="atualizarStatusTipo('${tipoId}')"
                                           class="quantidade-lote w-full px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <input type="hidden" name="pecas_extras[${novoIndex}][tipo_id]" value="${tipoId}">
                                    <input type="hidden" name="pecas_extras[${novoIndex}][tipo_nome]" value="${tipoNome}">
                                    <input type="hidden" name="pecas_extras[${novoIndex}][observacoes]" value="Peça extra: ${observacoes}">
                                    <input type="hidden" name="pecas_extras[${novoIndex}][peso]" value="0">
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="button" onclick="removerTipoExtra('${tipoId}')"
                                        class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                                        title="Remover tipo extra">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botão Adicionar Lote no final -->
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <button type="button" onclick="duplicarLoteEdicao('${tipoId}', '${tipoNome}')"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Novo Lote
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Inserir antes do botão "Adicionar Peça Extra"
        // Buscar o container pai que contém tanto os tipos quanto o botão
        const containerPai = document.querySelector('.bg-white.rounded-xl.shadow-sm') ||
                            document.querySelector('.bg-white.rounded-xl');
        const containerTipos = containerPai?.querySelector('.p-4');
        const botaoExtraContainer = document.querySelector('.p-4.border-t.border-gray-200.bg-gray-50');

        console.log('Container pai:', containerPai);
        console.log('Container tipos:', containerTipos);
        console.log('Botão extra container:', botaoExtraContainer);

        if (containerPai && botaoExtraContainer) {
            // Verificar se o botão é realmente filho do container pai
            if (containerPai.contains(botaoExtraContainer)) {
                containerPai.insertBefore(novoTipoContainer, botaoExtraContainer);
                console.log('Novo tipo extra inserido com sucesso antes do botão no container pai');
            } else {
                console.log('Botão não é filho do container pai, inserindo no final');
                containerPai.appendChild(novoTipoContainer);
                console.log('Novo tipo extra inserido no final do container pai');
            }
        } else {
            // Abordagem mais simples: sempre inserir no final do container de tipos
            const containerSeguro = containerTipos || containerPai;

            if (containerSeguro) {
                containerSeguro.appendChild(novoTipoContainer);
                console.log('Novo tipo extra inserido com sucesso usando abordagem segura');
            } else {
                // Último fallback: inserir em qualquer container disponível
                const qualquerContainer = document.querySelector('.container') ||
                                        document.querySelector('.max-w-7xl') ||
                                        document.querySelector('main') ||
                                        document.body;

                if (qualquerContainer) {
                    qualquerContainer.appendChild(novoTipoContainer);
                    console.log('Novo tipo extra inserido usando fallback extremo');
                } else {
                    console.error('Não foi possível encontrar NENHUM container para inserir o novo tipo');
                }
            }
        }

        // Verificar se foi realmente inserido
        setTimeout(() => {
            const verificacao = document.querySelector(`[data-tipo-id="${tipoId}"]`);
            if (verificacao) {
                console.log('✅ Verificação: Novo tipo foi inserido com sucesso na DOM');
            } else {
                console.error('❌ Verificação: Novo tipo NÃO foi inserido na DOM');
            }
        }, 100);
    };

    // Função para remover tipo extra completo
    window.removerTipoExtra = function(tipoId) {
        if (confirm('Tem certeza que deseja remover este tipo extra completo?')) {
            const tipoContainer = document.querySelector(`[data-tipo-id="${tipoId}"]`);
            if (tipoContainer) {
                tipoContainer.remove();
            }
        }
    };

    // Inicializar status de todos os tipos ao carregar a página
    function inicializarStatus() {
        document.querySelectorAll('.tipo-peca-container').forEach(container => {
            const headerOnclick = container.querySelector('.tipo-peca-header').onclick.toString();
            const tipoId = headerOnclick.match(/'(\d+)'/)[1];
            atualizarStatusTipo(tipoId);
        });
    }

    // Executar inicialização
    inicializarStatus();

    // Inicializar contadores globais
    atualizarContadoresGlobais();

    // Adicionar atalho de teclado (Ctrl+P para destacar pendentes)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            destacarLotesPendentes();
        }
    });
});
</script>
@endpush

@push('scripts')
<script>
// Tipos de peças disponíveis
const tiposDisponiveis = @json($tipos);
let contadorNovasPecas = 0;

// Função para adicionar nova linha de peça
function adicionarLinhaPeca() {
    const tabela = document.getElementById('tabela-pecas-empacotamento');
    
    // Criar opções do select
    let opcoesSelect = '<option value="">Selecione um tipo</option>';
    tiposDisponiveis.forEach(function(tipo) {
        opcoesSelect += `<option value="${tipo.id}">${tipo.nome} (${tipo.categoria})</option>`;
    });

    // Criar nova linha
    const novaLinha = document.createElement('tr');
    novaLinha.className = 'linha-nova-peca hover:bg-gray-50';
    novaLinha.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <select name="novas_pecas[${contadorNovasPecas}][tipo_id]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                    required>
                ${opcoesSelect}
            </select>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                Nova
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <input type="number" 
                   name="novas_pecas[${contadorNovasPecas}][quantidade]"
                   min="1" 
                   placeholder="1"
                   class="w-20 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                   required>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <input type="number" 
                   name="novas_pecas[${contadorNovasPecas}][peso]"
                   step="0.01" 
                   min="0"
                   placeholder="0.00"
                   class="w-20 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <button type="button" 
                    onclick="removerLinhaPeca(this)"
                    class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors duration-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Remover
            </button>
        </td>
    `;

    // Adicionar a linha à tabela
    tabela.appendChild(novaLinha);
    contadorNovasPecas++;
}

// Função para remover linha de peça
function removerLinhaPeca(botao) {
    const linha = botao.closest('tr');
    if (linha.classList.contains('linha-nova-peca')) {
        linha.remove();
    }
}
</script>
@endpush
