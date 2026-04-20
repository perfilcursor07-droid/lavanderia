@extends('layouts.app')

@section('title', 'Detalhes do Empacotamento')

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                📦 Empacotamento {{ $empacotamento->codigo_qr }}
            </h1>
            <p class="text-sm text-gray-600">Detalhes do empacotamento e rastreamento</p>
        </div>
        <div class="flex flex-wrap gap-2 mt-3 sm:mt-0">
            @if($empacotamento->status->nome !== 'Entregue')
                @if($empacotamento->status->nome === 'Pronto para entrega')
                    <button onclick="concluirEmpacotamento()" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Concluir Empacotamento
                    </button>
                @endif
                
                <a href="{{ route('empacotamento.edit', $empacotamento->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
            @endif

            <a href="{{ route('empacotamento.etiqueta', $empacotamento->id) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Gerar Etiqueta
            </a>

            <a href="{{ route('empacotamento.reimprimir-qr', $empacotamento->id) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Reimprimir QR
            </a>

            <a href="{{ route('empacotamento.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Detalhes do Empacotamento -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Detalhes do Empacotamento
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Código: <span class="font-mono font-bold text-blue-600">{{ $empacotamento->codigo_qr }}</span></p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Status Atual -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Status Atual</h4>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium"
                                      style="background-color: {{ $empacotamento->status->cor }}20; color: {{ $empacotamento->status->cor }};">
                                    <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $empacotamento->status->cor }};"></div>
                                    {{ $empacotamento->status->nome }}
                                </span>
                            </div>
                        </div>

                        <!-- Data de Empacotamento -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Data de Empacotamento</h4>
                            <div class="text-lg font-bold text-gray-900">{{ $empacotamento->data_empacotamento->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-600">{{ $empacotamento->data_empacotamento->format('H:i:s') }}</div>
                        </div>

                        <!-- Responsável -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Responsável pelo Empacotamento</h4>
                            <div class="text-gray-900 font-medium">{{ $empacotamento->usuarioEmpacotamento->nome }}</div>
                        </div>
                    </div>

                    @if($empacotamento->observacoes_empacotamento)
                        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h4 class="font-semibold text-yellow-800 mb-2">Observações do Empacotamento</h4>
                            <p class="text-yellow-700">{{ $empacotamento->observacoes_empacotamento }}</p>
                        </div>
                    @endif

                    @if($empacotamento->estaEmAberto())
                        @php
                            $pecasFaltando = $empacotamento->getPecasFaltandoEmpacotar();
                        @endphp
                        <div class="mt-6 p-4 bg-orange-50 border border-orange-300 rounded-lg">
                            <h4 class="font-semibold text-orange-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                ⚠️ Empacotamento em Aberto - Peças Pendentes
                            </h4>
                            <p class="text-orange-700 mb-3">Ainda há peças que não foram totalmente empacotadas:</p>
                            <div class="space-y-2">
                                @foreach($pecasFaltando as $peca)
                                    <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $peca['tipo']->nome }}</span>
                                            <span class="text-sm text-gray-600">({{ $peca['tipo']->categoria }})</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-orange-700 font-semibold">
                                                Faltam: {{ $peca['quantidade_faltando'] }} peça(s)
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                Coletadas: {{ $peca['quantidade_coletada'] }} | Empacotadas: {{ $peca['quantidade_empacotada'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <a href="{{ route('empacotamento.edit', $empacotamento->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Continuar Empacotamento
                                </a>
                                <form method="POST" action="{{ route('empacotamento.update', $empacotamento->id) }}" 
                                      onsubmit="return confirm('Tem certeza que deseja finalizar o empacotamento com peças faltando? Esta ação marcará como Pronto para Motorista.');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="data_empacotamento" value="{{ $empacotamento->data_empacotamento->format('Y-m-d\TH:i') }}">
                                    <input type="hidden" name="forcar_finalizacao" value="1">
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Finalizar mesmo assim
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações do Hotel/Estabelecimento -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Informações do Hotel
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold text-blue-900 text-lg">Hotel: {{ $empacotamento->coleta->estabelecimento->razao_social }}</h4>
                                @if($empacotamento->coleta->estabelecimento->nome_fantasia)
                                    <p class="text-blue-700 text-sm mt-1">{{ $empacotamento->coleta->estabelecimento->nome_fantasia }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $empacotamento->coleta->numero_coleta }}</div>
                            <div class="text-sm text-gray-600">Número da Coleta</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($empacotamento->coleta->peso_total, 1, ',', '.') }} kg</div>
                            <div class="text-sm text-gray-600">Peso Total</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">R$ {{ number_format($empacotamento->coleta->valor_total, 2, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Valor Total</div>
                        </div>
                    </div>

                    @if($empacotamento->coleta->estabelecimento && $empacotamento->coleta->estabelecimento->tipo_precificacao === 'peca')
                    <div class="mt-4 p-4 bg-amber-50 border-2 border-amber-200 rounded-lg text-center">
                        <div class="text-sm font-medium text-gray-700 mb-2">💰 Valor Calculado (Por Peça)</div>
                        <div class="text-3xl font-bold text-amber-600">{{ $empacotamento->valor_formatado }}</div>
                        <div class="text-sm text-gray-600 mt-2">
                            {{ $empacotamento->pecasIndividuais->sum('quantidade') }} peças × R$ {{ number_format($empacotamento->coleta->estabelecimento->preco_peca, 2, ',', '.') }}/peça
                        </div>
                    </div>
                    @endif

                    @if($empacotamento->coleta->estabelecimento->endereco)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">Endereço:</div>
                            <div class="text-gray-900">{{ $empacotamento->coleta->estabelecimento->endereco }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Peças Coletadas e Empacotadas -->
            @if($empacotamento->coleta->pecas->count() > 0 || $empacotamento->pecasIndividuais->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Peças Coletadas e Empacotadas
                            <span class="ml-2 bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $empacotamento->pecasIndividuais->count() }} peças
                            </span>
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Relação detalhada das peças processadas</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Peça</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">Código QR</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">Quantidade</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">Peso (kg)</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">Categoria</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    // Calcular peças faltando por tipo para colorir
                                    $pecasFaltandoPorTipo = [];
                                    if ($empacotamento->estaEmAberto()) {
                                        $faltando = $empacotamento->getPecasFaltandoEmpacotar();
                                        foreach ($faltando as $f) {
                                            $pecasFaltandoPorTipo[$f['tipo']->id] = $f['quantidade_faltando'];
                                        }
                                    }
                                @endphp
                                @foreach($empacotamento->pecasIndividuais as $peca)
                                    @php
                                        $tipoId = $peca->tipo_id;
                                        $temFaltando = isset($pecasFaltandoPorTipo[$tipoId]) && $pecasFaltandoPorTipo[$tipoId] > 0;
                                        $corLinha = $temFaltando ? 'bg-red-50 hover:bg-red-100' : 'bg-green-50 hover:bg-green-100';
                                        $corBolinha = $temFaltando ? 'bg-red-500' : 'bg-green-500';
                                    @endphp
                                    <tr class="{{ $corLinha }} transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 {{ $corBolinha }} rounded-full mr-3"></div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $peca->tipo ? $peca->tipo->nome : 'Tipo não definido' }}</div>
                                                    @if($peca->observacoes)
                                                        <div class="text-xs text-gray-500">{{ Str::limit($peca->observacoes, 30) }}</div>
                                                    @endif
                                                    @if($temFaltando)
                                                        <div class="text-xs text-red-600 font-medium">⚠ Faltam {{ $pecasFaltandoPorTipo[$tipoId] }} peça(s)</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                                {{ $peca->codigo_qr }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                {{ $peca->quantidade }} peça{{ $peca->quantidade > 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 font-medium">
                                            {{ number_format($peca->peso, 3, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $peca->tipo ? $peca->tipo->categoria : 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total:</td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                        {{ $empacotamento->pecasIndividuais->sum('quantidade') }} peças
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                        {{ number_format($empacotamento->pecasIndividuais->sum('peso'), 3, ',', '.') }} kg
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                        {{ $empacotamento->pecasIndividuais->groupBy('tipo.categoria')->count() }} categorias
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-6">


            <!-- QR Codes Individuais das Peças -->
            @php
                $pecasDisponiveis = $empacotamento->pecasIndividuais->where('status_saida', '!=', 'em_transito');
            @endphp
            @if($pecasDisponiveis->count() > 0)
                <div id="qr-codes-pecas" class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h3a1 1 0 011 1v2h4a1 1 0 011 1v3a1 1 0 01-1 1h-2v9a1 1 0 01-1 1H8a1 1 0 01-1-1V9H5a1 1 0 01-1-1V5a1 1 0 011-1h2z"></path>
                            </svg>
                            QR Codes das Peças
                            <span class="ml-2 bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $pecasDisponiveis->count() }}
                            </span>
                            @if($empacotamento->pecasIndividuais->where('status_saida', 'em_transito')->count() > 0)
                                <span class="ml-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $empacotamento->pecasIndividuais->where('status_saida', 'em_transito')->count() }} em trânsito
                                </span>
                            @endif
                        </h3>
                        <p class="text-xs text-gray-600 mt-1">
                            💡 Exibindo apenas sacolas que ainda não saíram para entrega
                        </p>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($pecasDisponiveis as $peca)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            {!! QrCode::size(100)->generate($peca->codigo_qr) !!}
                                        </div>
                                        <div class="text-sm font-mono font-bold text-purple-600 mb-2">
                                            {{ $peca->codigo_qr }}
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $peca->tipo->nome }}
                                        </div>
                                        <div class="text-xs text-gray-500 mb-1">
                                            {{ $peca->tipo->categoria }}
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            <strong>{{ $peca->quantidade }}</strong> peça{{ $peca->quantidade > 1 ? 's' : '' }}
                                        </div>
                                        @if($peca->peso > 0)
                                            <div class="text-xs text-gray-500">
                                                {{ number_format($peca->peso, 3, ',', '.') }} kg
                                            </div>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('qrcodes.rastrear-peca', $peca->codigo_qr) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-medium rounded-full transition-colors duration-200"
                                               target="_blank">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Rastrear
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Ações para imprimir todos os QR codes das peças -->
                        <div class="mt-4 text-center border-t border-gray-200 pt-4">
                            <button onclick="imprimirQRCodesPecas()" 
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Imprimir QR Codes das Peças
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Informações sobre sacolas em trânsito -->
            @if($empacotamento->pecasIndividuais->where('status_saida', 'em_transito')->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sacolas em Trânsito
                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $empacotamento->pecasIndividuais->where('status_saida', 'em_transito')->count() }}
                            </span>
                        </h3>
                        <p class="text-xs text-gray-600 mt-1">
                            🚚 Sacolas que já saíram com o motorista
                        </p>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2">
                            @foreach($empacotamento->pecasIndividuais->where('status_saida', 'em_transito') as $peca)
                                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <div>
                                            <div class="font-medium text-sm text-gray-900">{{ $peca->tipo->nome }}</div>
                                            <div class="text-xs text-gray-600">{{ $peca->quantidade }} peças • {{ $peca->codigo_qr }}</div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-green-600 font-medium">
                                        @if($peca->data_saida)
                                            {{ $peca->data_saida->format('d/m H:i') }}
                                        @else
                                            Em trânsito
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informações de Entrega -->
            @if($empacotamento->status->nome === 'Entregue')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Entrega Realizada
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-green-800 font-medium">Empacotamento entregue com sucesso</p>
                            <p class="text-green-600 text-sm mt-1">Processo finalizado</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Aguardando Entrega -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Aguardando Entrega
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                            <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-yellow-800 font-medium">Aguardando Entrega</p>
                            <p class="text-yellow-600 text-sm mt-1">O empacotamento ainda não foi entregue</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- QR Code -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        Código QR
                    </h3>
                </div>
                <div class="p-4 text-center">
                    <div class="text-sm text-gray-600 mb-2">{{ $empacotamento->codigo_qr }}</div>
                    <div class="text-xs text-gray-500">Use este código para rastreamento</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Entrega -->
@if($empacotamento->podeSerEntregue() && !$empacotamento->foiEntregue())
<div id="modalEntrega" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Entrega</h3>
            <form method="POST" action="{{ route('empacotamento.confirmar-entrega', $empacotamento->id) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="nome_recebedor" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome do Recebedor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nome_recebedor" id="nome_recebedor" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="data_entrega" class="block text-sm font-medium text-gray-700 mb-2">
                        Data/Hora da Entrega <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="data_entrega" id="data_entrega" required
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="observacoes_entrega" class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea name="observacoes_entrega" id="observacoes_entrega" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                              placeholder="Observações sobre a entrega..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="fecharModalEntrega()" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                        Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function abrirModalEntrega() {
    document.getElementById('modalEntrega').classList.remove('hidden');
}

function fecharModalEntrega() {
    document.getElementById('modalEntrega').classList.add('hidden');
}

// Fechar modal ao clicar fora
const modalEntrega = document.getElementById('modalEntrega');
if (modalEntrega) {
    modalEntrega.addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalEntrega();
        }
    });
}

// Função para concluir empacotamento
function concluirEmpacotamento() {
    if (confirm('Tem certeza que deseja concluir este empacotamento? Ele ficará pronto para o motorista.')) {
        // Implementar AJAX para concluir empacotamento
        fetch('{{ route("empacotamento.concluir", $empacotamento->id) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao concluir empacotamento');
        });
    }
}

function imprimirQRCodesPecas() {
    // Criar uma nova janela para impressão dos QR codes das peças
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    let content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Codes das Peças - {{ $empacotamento->codigo_qr }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
                .qr-item { 
                    border: 1px solid #ddd; 
                    padding: 15px; 
                    text-align: center; 
                    border-radius: 8px; 
                    page-break-inside: avoid;
                }
                .qr-code { margin-bottom: 10px; }
                .codigo { font-family: monospace; font-weight: bold; margin-bottom: 10px; }
                .tipo { font-weight: bold; margin-bottom: 5px; }
                .info { font-size: 14px; color: #666; }
                @media print {
                    body { margin: 10px; }
                    .grid { gap: 15px; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>QR Codes das Peças</h2>
                <p><strong>Empacotamento:</strong> {{ $empacotamento->codigo_qr }}</p>
                <p><strong>Estabelecimento:</strong> {{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                <p><strong>Data:</strong> {{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
            </div>
            <div class="grid">`;
    
    @foreach($empacotamento->pecasIndividuais as $peca)
        content += `
            <div class="qr-item">
                <div class="qr-code">
                    {!! addslashes(QrCode::size(120)->generate($peca->codigo_qr)) !!}
                </div>
                <div class="codigo">{{ $peca->codigo_qr }}</div>
                <div class="tipo">{{ $peca->tipo->nome }}</div>
                <div class="info">{{ $peca->tipo->categoria }}</div>
                <div class="info">{{ $peca->quantidade }} peça{{ $peca->quantidade > 1 ? 's' : '' }}</div>
                @if($peca->peso > 0)
                    <div class="info">{{ number_format($peca->peso, 3, ',', '.') }} kg</div>
                @endif
            </div>`;
    @endforeach
    
    content += `
            </div>
        </body>
        </html>`;
    
    printWindow.document.write(content);
    printWindow.document.close();
    
    printWindow.onload = function() {
        printWindow.print();
    };
}

// ============ AUTO-REFRESH PARA ATUALIZAÇÕES EM TEMPO REAL ============

let ultimaVerificacao = Date.now();
let intervaloVerificacao = null;

function verificarAtualizacoesSacolas() {
    // Buscar mudanças via AJAX sem recarregar a página completa
    fetch(window.location.href, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Criar um documento temporário para analisar a resposta
        const parser = new DOMParser();
        const novoDoc = parser.parseFromString(html, 'text/html');
        
        // Extrair as seções que podem ter mudado
        const novoQRCodes = novoDoc.querySelector('#qr-codes-pecas');
        const novasSacolasTransito = novoDoc.querySelector('[class*="Sacolas em Trânsito"]')?.closest('.bg-white');
        
        // Atualizar seção QR Codes das Peças
        const qrAtual = document.querySelector('#qr-codes-pecas');
        if (novoQRCodes && qrAtual) {
            const qrAtualHTML = qrAtual.outerHTML;
            const novoQRHTML = novoQRCodes.outerHTML;
            
            if (qrAtualHTML !== novoQRHTML) {
                console.log('🔄 Atualizando QR Codes das Peças...');
                qrAtual.outerHTML = novoQRHTML;
                mostrarNotificacaoAtualizacao('📦 Lista de sacolas atualizada!');
            }
        }
        
        // Se não há mais QR codes para mostrar, remover a seção
        if (!novoQRCodes && qrAtual) {
            console.log('✅ Todas as sacolas saíram! Removendo seção...');
            qrAtual.remove();
            mostrarNotificacaoAtualizacao('🎉 Todas as sacolas estão em trânsito!');
        }
        
        // Atualizar ou criar seção de Sacolas em Trânsito
        atualizarSacolasTransito(novoDoc);
        
    })
    .catch(error => {
        console.error('❌ Erro ao verificar atualizações:', error);
    });
}

function atualizarSacolasTransito(novoDoc) {
    // Buscar a nova seção de sacolas em trânsito
    const novaSecaoTransito = Array.from(novoDoc.querySelectorAll('h3'))
        .find(h3 => h3.textContent.includes('Sacolas em Trânsito'))
        ?.closest('.bg-white');
    
    // Buscar a seção atual
    const secaoAtualTransito = Array.from(document.querySelectorAll('h3'))
        .find(h3 => h3.textContent.includes('Sacolas em Trânsito'))
        ?.closest('.bg-white');
    
    if (novaSecaoTransito && secaoAtualTransito) {
        // Atualizar seção existente
        const htmlAtual = secaoAtualTransito.outerHTML;
        const novoHTML = novaSecaoTransito.outerHTML;
        
        if (htmlAtual !== novoHTML) {
            console.log('🚚 Atualizando Sacolas em Trânsito...');
            secaoAtualTransito.outerHTML = novoHTML;
            mostrarNotificacaoAtualizacao('🚚 Sacolas em trânsito atualizadas!');
        }
    } else if (novaSecaoTransito && !secaoAtualTransito) {
        // Criar nova seção se não existir
        console.log('➕ Criando seção Sacolas em Trânsito...');
        const qrCodesSection = document.querySelector('#qr-codes-pecas')?.closest('.bg-white');
        if (qrCodesSection) {
            qrCodesSection.insertAdjacentHTML('afterend', novaSecaoTransito.outerHTML);
            mostrarNotificacaoAtualizacao('➕ Nova sacola em trânsito!');
        }
    }
}

function mostrarNotificacaoAtualizacao(mensagem) {
    // Criar notificação visual elegante
    const notificacao = document.createElement('div');
    notificacao.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
    notificacao.innerHTML = `
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium">${mensagem}</span>
        </div>
    `;
    
    document.body.appendChild(notificacao);
    
    // Animar entrada
    setTimeout(() => {
        notificacao.classList.remove('translate-x-full');
    }, 100);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notificacao.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notificacao)) {
                document.body.removeChild(notificacao);
            }
        }, 300);
    }, 3000);
}

function iniciarMonitoramento() {
    console.log('🔄 Iniciando monitoramento de atualizações...');
    
    // Verificar a cada 5 segundos
    intervaloVerificacao = setInterval(verificarAtualizacoesSacolas, 5000);
    
    // Verificar também quando a página ganha foco
    window.addEventListener('focus', verificarAtualizacoesSacolas);
    
    // Mostrar indicador de monitoramento ativo
    mostrarIndicadorMonitoramento();
}

function mostrarIndicadorMonitoramento() {
    const indicador = document.createElement('div');
    indicador.id = 'indicador-monitoramento';
    indicador.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-3 py-2 rounded-full shadow-lg z-40 text-xs font-medium';
    indicador.innerHTML = `
        <div class="flex items-center">
            <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
            Monitorando atualizações
        </div>
    `;
    
    document.body.appendChild(indicador);
}

function pararMonitoramento() {
    if (intervaloVerificacao) {
        clearInterval(intervaloVerificacao);
        intervaloVerificacao = null;
        console.log('⏹️ Monitoramento parado');
        
        const indicador = document.getElementById('indicador-monitoramento');
        if (indicador) {
            indicador.remove();
        }
    }
}

// Iniciar monitoramento quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar 2 segundos antes de iniciar o monitoramento
    setTimeout(iniciarMonitoramento, 2000);
});

// Parar monitoramento quando sair da página
window.addEventListener('beforeunload', pararMonitoramento);

console.log('📱 Auto-refresh configurado para empacotamento {{ $empacotamento->codigo_qr }}');

</script>
@endpush
@endsection
