@extends('layouts.public')

@section('title', 'Resultados da Busca')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30">
    <!-- Header Compacto -->
    <div class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-white/20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Resultados</h1>
                        <div class="flex items-center space-x-2 text-xs text-gray-600">
                            <span>{{ $coletas->count() }} coleta(s)</span>
                            <span>•</span>
                            <span>"{{ $termoBusca }}"</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('acompanhamento.index') }}" 
                   class="text-gray-700 hover:bg-white px-3 py-2 rounded-lg transition-all duration-300 text-sm font-medium">
                    <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Nova Busca</span>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="space-y-4">
            @foreach($coletas as $coleta)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 border border-gray-100 overflow-hidden">
                    <!-- Dados da Empresa no Topo -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50/30 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">#</span>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900">{{ $coleta->numero_coleta }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          style="background-color: {{ $coleta->status->cor }}20; color: {{ $coleta->status->cor }};">
                                        {{ $coleta->status->nome }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-medium">{{ $coleta->estabelecimento->razao_social }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>{{ $coleta->estabelecimento->cnpj_formatado ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $coleta->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etapas Compactas -->
                    <div class="p-4">
                        @php
                            // Sistema de 5 etapas (20% cada)
                            $progressoEtapas = [
                                'coleta' => true, // Sempre concluída se existe
                                'pesagem' => $coleta->pesagens->count() > 0,
                                'empacotamento' => $coleta->empacotamentos->isNotEmpty(),
                                'entrega' => false,
                                'confirmacao_cliente' => false
                            ];

                            // Verificar entrega e confirmação
                            if($coleta->empacotamentos->isNotEmpty()) {
                                $statusEmpacotamento = $coleta->empacotamentos->first()->status->nome;
                                
                                // Entrega concluída se empacotamento está pronto, em trânsito ou entregue
                                if(in_array($statusEmpacotamento, ['Pronto para motorista', 'Em trânsito', 'Entregue'])) {
                                    $progressoEtapas['entrega'] = true;
                                }
                                
                                // Confirmação concluída se está entregue
                                if($statusEmpacotamento === 'Entregue') {
                                    $progressoEtapas['confirmacao_cliente'] = true;
                                }
                            }

                            // Calcular percentual: cada etapa vale 20%
                            $etapasConcluidas = collect($progressoEtapas)->filter()->count();
                            $progresso = round(($etapasConcluidas / 5) * 100);
                        @endphp

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex-1">
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $progresso }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>{{ $etapasConcluidas }}/5 etapas</span>
                                    <span class="font-medium">{{ $progresso }}%</span>
                                </div>
                            </div>
                            <a href="{{ route('acompanhamento.detalhes', $coleta->id) }}" 
                               class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-300 text-sm font-medium">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Ver Detalhes</span>
                                </span>
                            </a>
                        </div>

                        <!-- Mini etapas -->
                        <div class="grid grid-cols-5 gap-2">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full {{ $progressoEtapas['coleta'] ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    @if($progressoEtapas['coleta'])
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-xs mt-1 {{ $progressoEtapas['coleta'] ? 'text-green-600' : 'text-gray-400' }}">Coleta</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full {{ $progressoEtapas['pesagem'] ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    @if($progressoEtapas['pesagem'])
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-xs mt-1 {{ $progressoEtapas['pesagem'] ? 'text-green-600' : 'text-gray-400' }}">Pesagem</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full {{ $progressoEtapas['empacotamento'] ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    @if($progressoEtapas['empacotamento'])
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-xs mt-1 {{ $progressoEtapas['empacotamento'] ? 'text-green-600' : 'text-gray-400' }}">Empacot.</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full {{ $progressoEtapas['entrega'] ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    @if($progressoEtapas['entrega'])
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-xs mt-1 {{ $progressoEtapas['entrega'] ? 'text-green-600' : 'text-gray-400' }}">Trânsito</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full {{ $progressoEtapas['confirmacao_cliente'] ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    @if($progressoEtapas['confirmacao_cliente'])
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-xs mt-1 {{ $progressoEtapas['confirmacao_cliente'] ? 'text-green-600' : 'text-gray-400' }}">Entregue</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($coletas->isEmpty())
            <div class="text-center py-20">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Nenhuma coleta encontrada</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Não encontramos coletas para <strong>"{{ $termoBusca }}"</strong>.<br>
                        Verifique se o CNPJ ou número da coleta estão corretos.
                    </p>
                    
                    <!-- Dicas de busca -->
                    <div class="bg-blue-50 rounded-2xl p-6 mb-8 text-left">
                        <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dicas de busca:
                        </h4>
                        <ul class="text-blue-800 text-sm space-y-2">
                            <li class="flex items-center space-x-2">
                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                <span>Use o CNPJ completo: XX.XXX.XXX/XXXX-XX</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                <span>Ou o número da coleta: COL-2024-XXX</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                <span>Verifique se não há espaços extras</span>
                            </li>
                        </ul>
                    </div>
                    
                    <a href="{{ route('acompanhamento.index') }}" 
                       class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-2xl transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Fazer Nova Busca</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection