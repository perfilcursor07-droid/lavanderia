@extends(auth()->user()->nivelAcesso && auth()->user()->nivelAcesso->nome === 'Motorista' ? 'layouts.motorista' : 'layouts.app')

@section('title', 'Gestão de Entregas')

@push('styles')
<style>
    /* Estilos para scanner QR */
    #qr-reader {
        width: 100%;
    }
    #qr-reader__dashboard_section_csr {
        background: #f8f9fa !important;
    }
    .qr-scanner-container {
        position: relative;
        max-width: 400px;
        margin: 0 auto;
        border: 2px solid #3B82F6;
        border-radius: 8px;
        overflow: hidden;
        background: #000;
    }
    
    #qr-reader {
        width: 100% !important;
        height: 300px !important;
        border: none !important;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #qr-reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 6px;
    }
    
    /* Melhorias para mobile */
    @media (max-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .md\:grid-cols-2 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        .lg\:grid-cols-3 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }
</style>
@endpush

@push('scripts')
<!-- jsQR - Biblioteca mais simples e confiável -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Entregas</h1>
                <p class="text-gray-600 text-sm">Gerencie as sacolas individuais para entrega</p>
            </div>
            <div class="mt-3 sm:mt-0 sm:ml-4">
                <button onclick="abrirScannerQR()" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M9 16h.01M15 16h.01M16 12h1M8 12H7m5 5v3m-5-2h10a1 1 0 001-1V8a1 1 0 00-1-1H6a1 1 0 00-1 1v9a1 1 0 001 1z"></path>
                    </svg>
                    📱 Escanear QR Code
                </button>
            </div>
        </div>
        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="text-blue-800 text-sm">
                <span class="font-medium">📦 Scanner QR Code:</span> 
                Use o botão <strong>"📱 Escanear QR Code"</strong> para ler:
            </p>
            <ul class="text-blue-700 text-xs mt-2 ml-4 list-disc">
                <li><strong>QR do Empacotamento</strong> (EMP...) - Confirma saída de todas as sacolas</li>
                <li><strong>QR da Sacola Individual</strong> (PC...) - Confirma saída de uma sacola específica</li>
            </ul>
        </div>
    </div>

    <!-- Cards de Estatísticas - Compactos -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-green-100">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Sacolas Prontas</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalSacolasProntas ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $prontos }} empacotamentos</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-blue-100">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Sacolas em Trânsito</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalSacolasTransito ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $emTransito }} empacotamentos</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-purple-100">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Hoje</p>
                    <p class="text-xl font-bold text-gray-900">{{ $entreguesHoje }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-gray-100">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Total</p>
                    <p class="text-xl font-bold text-gray-900">{{ $total }}</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Abas Principais -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-2 px-4">
                <button onclick="showMainTab('prontos')" id="tab-prontos"
                        class="main-tab-button py-3 px-4 border-b-2 border-green-500 font-medium text-sm text-green-600">
                    📦 Sacolas Prontas ({{ $totalSacolasProntas ?? 0 }})
                </button>
                <button onclick="showMainTab('transito')" id="tab-transito"
                        class="main-tab-button py-3 px-4 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    🚚 Sacolas em Trânsito ({{ $totalSacolasTransito ?? 0 }})
                </button>
                <button onclick="showMainTab('entregues')" id="tab-entregues"
                        class="main-tab-button py-3 px-4 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    ✅ Entregues ({{ $entreguesHoje + ($pecasEntreguesHoje->count() ?? 0) }})
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-4">


            <!-- Prontos para Entrega -->
            <div id="content-prontos" class="main-tab-content hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">📦 Sacolas Prontas para Entrega</h3>
                    <p class="text-gray-600 text-sm">Confirme a saída de cada sacola individualmente - cada sacola contém um tipo específico de peça</p>
                </div>
                @forelse($empacotamentosProntos as $empacotamento)
                    @if($empacotamento->pecasIndividuais && $empacotamento->pecasIndividuais->count() > 0)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                            <!-- Cabeçalho do Empacotamento -->
                            <div class="flex items-start justify-between mb-3 pb-3 border-b border-gray-300">
                                <div>
                                    <h4 class="font-bold text-base text-gray-900">
                                        🏢 {{ $empacotamento->coleta?->estabelecimento?->nome_fantasia ?? $empacotamento->coleta?->estabelecimento?->razao_social ?? 'Estabelecimento não encontrado' }}
                                    </h4>
                                    <p class="text-gray-600 text-sm">
                                        <span class="font-medium">Empacotamento:</span> {{ $empacotamento->codigo_qr }}
                                    </p>
                                    <p class="text-gray-500 text-xs">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $empacotamento->pecasIndividuais->count() }} sacola{{ $empacotamento->pecasIndividuais->count() > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Sacolas (Peças Individuais) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($empacotamento->pecasIndividuais as $peca)
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 hover:border-blue-300 transition-colors">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <h5 class="font-semibold text-sm text-gray-900">
                                                    🏷️ {{ $peca->tipo->nome }}
                                                </h5>
                                                <p class="text-xs text-gray-500 mb-1">{{ $peca->tipo->categoria }}</p>
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-medium">Qtd:</span> {{ $peca->quantidade }} peça{{ $peca->quantidade > 1 ? 's' : '' }}
                                                </p>
                                                @if($peca->peso > 0)
                                                    <p class="text-xs text-gray-600">
                                                        <span class="font-medium">Peso:</span> {{ number_format($peca->peso, 3, ',', '.') }} kg
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="text-center bg-gray-100 rounded-lg p-2">
                                                <div class="text-xs font-mono font-bold text-purple-600 mb-1">
                                                    {{ $peca->codigo_qr }}
                                                </div>
                                                <div class="text-xs text-gray-500">QR Code da Sacola</div>
                                            </div>
                                        </div>

                                        <button onclick="confirmarSaidaSacola('{{ $peca->codigo_qr }}', '{{ $peca->tipo->nome }}', {{ $empacotamento->id }})"
                                                class="w-full px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                            🚚 Confirmar Saída
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Ação em lote para o empacotamento completo -->
                            <div class="mt-3 pt-3 border-t border-gray-300">
                                <button onclick="confirmarSaidaCompleta({{ $empacotamento->id }})"
                                        class="w-full px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                    🚚 CONFIRMAR TODAS AS SACOLAS → EM TRÂNSITO
                                </button>
                                <p class="text-xs text-gray-500 mt-1 text-center">
                                    Move todas as {{ $empacotamento->pecasIndividuais->count() }} sacola{{ $empacotamento->pecasIndividuais->count() > 1 ? 's' : '' }} para "Sacolas em Trânsito" de uma vez
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Empacotamento sem peças individuais (sistema legado) -->
                        <div class="border border-gray-200 rounded-lg p-3 mb-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-bold text-base">{{ $empacotamento->codigo_qr }}</h3>
                                    <p class="text-blue-600 font-medium text-sm">{{ $empacotamento->status->nome }}</p>
                                    @if($empacotamento->coleta && $empacotamento->coleta->estabelecimento)
                                        <p class="text-gray-900 font-medium text-sm">{{ Str::limit($empacotamento->coleta->estabelecimento->nome_fantasia ?? $empacotamento->coleta->estabelecimento->razao_social, 30) }}</p>
                                    @else
                                        <p class="text-red-600 font-medium text-sm">Estabelecimento não encontrado</p>
                                    @endif
                                    <p class="text-gray-600 text-xs">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⚠️ Empacotamento Legado (sem sacolas individuais)
                                        </span>
                                    </div>
                                </div>
                                <button onclick="confirmarSaida({{ $empacotamento->id }})"
                                        class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ml-2">
                                    Confirmar Saída
                                </button>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📦</div>
                        <p class="text-gray-500 text-lg">Nenhuma sacola pronta para entrega</p>
                        <p class="text-gray-400 text-sm">As sacolas aparecerão aqui quando os empacotamentos estiverem prontos</p>
                    </div>
                @endforelse
            </div>

            <!-- Em Trânsito -->
            <div id="content-transito" class="main-tab-content hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">🚚 Sacolas Em Trânsito</h3>
                    <p class="text-gray-600 text-sm">Confirme a entrega de cada empacotamento completo com assinatura</p>
                </div>
                @forelse($empacotamentosTransito as $empacotamento)
                    @if($empacotamento->pecasIndividuais && $empacotamento->pecasIndividuais->count() > 0)
                        <div class="border border-yellow-200 rounded-lg p-4 mb-4 bg-yellow-50">
                            <!-- Cabeçalho do Empacotamento -->
                            <div class="flex items-start justify-between mb-3 pb-3 border-b border-yellow-300">
                                <div>
                                    <h4 class="font-bold text-base text-gray-900">
                                        🏢 {{ $empacotamento->coleta?->estabelecimento?->nome_fantasia ?? $empacotamento->coleta?->estabelecimento?->razao_social ?? 'Estabelecimento não encontrado' }}
                                    </h4>
                                    <p class="text-gray-600 text-sm">
                                        <span class="font-medium">Empacotamento:</span> {{ $empacotamento->codigo_qr }}
                                    </p>
                                    @if($empacotamento->entrega && $empacotamento->entrega->data_saida)
                                        <p class="text-gray-500 text-xs">Saída: {{ $empacotamento->entrega->data_saida->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        🚚 {{ $empacotamento->pecasIndividuais->count() }} sacola{{ $empacotamento->pecasIndividuais->count() > 1 ? 's' : '' }} em trânsito
                                    </span>
                                </div>
                            </div>

                            <!-- Sacolas (Peças Individuais) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
                                @foreach($empacotamento->pecasIndividuais as $peca)
                                    <div class="bg-white border border-yellow-200 rounded-lg p-3 peca-entrega-item" data-peca-id="{{ $peca->id }}" data-empacotamento-id="{{ $empacotamento->id }}">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-start space-x-2 flex-1">
                                                <input type="checkbox" 
                                                       class="peca-checkbox mt-1 w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                                       data-peca-id="{{ $peca->id }}"
                                                       data-empacotamento-id="{{ $empacotamento->id }}"
                                                       onchange="atualizarSelecaoEntrega({{ $empacotamento->id }})">
                                                <div>
                                                    <h5 class="font-semibold text-sm text-gray-900">
                                                        🏷️ {{ $peca->tipo->nome }}
                                                    </h5>
                                                    <p class="text-xs text-gray-500 mb-1">{{ $peca->tipo->categoria }}</p>
                                                    <p class="text-xs text-gray-600">
                                                        <span class="font-medium">Qtd:</span> {{ $peca->quantidade }} peça{{ $peca->quantidade > 1 ? 's' : '' }}
                                                    </p>
                                                    @if($peca->peso > 0)
                                                        <p class="text-xs text-gray-600">
                                                            <span class="font-medium">Peso:</span> {{ number_format($peca->peso, 3, ',', '.') }} kg
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <div class="text-center bg-purple-100 rounded-lg p-2">
                                                <div class="text-xs font-mono font-bold text-purple-600 mb-1">
                                                    {{ $peca->codigo_qr }}
                                                </div>
                                                <div class="text-xs text-purple-600">
                                                    <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-purple-200">
                                                        ✅ Em trânsito
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Ações de entrega -->
                            <div class="mt-3 pt-3 border-t border-yellow-300 space-y-2">
                                <!-- Selecionar todas -->
                                <div class="flex items-center justify-between mb-2">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" 
                                               class="selecionar-todas-pecas w-4 h-4 text-green-600 border-gray-300 rounded"
                                               onchange="selecionarTodasPecas({{ $empacotamento->id }}, this.checked)">
                                        <span class="text-sm text-gray-700">Selecionar todas</span>
                                    </label>
                                    <span class="text-xs text-gray-500" id="contador-selecionadas-{{ $empacotamento->id }}">0 de {{ $empacotamento->pecasIndividuais->count() }} selecionadas</span>
                                </div>
                                
                                <!-- Botão entregar selecionadas -->
                                <button onclick="abrirModalEntregaSelecionadas({{ $empacotamento->id }}, '{{ $empacotamento->codigo_qr }}')"
                                        id="btn-entregar-selecionadas-{{ $empacotamento->id }}"
                                        class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors hidden">
                                    📋 Entregar Selecionadas (<span id="total-selecionadas-{{ $empacotamento->id }}">0</span> sacolas)
                                </button>
                                
                                <!-- Botão entregar todas -->
                                <button onclick="abrirModalEntrega({{ $empacotamento->id }}, '{{ $empacotamento->codigo_qr }}')"
                                        class="w-full px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    ✅ Entregar Todas ({{ $empacotamento->pecasIndividuais->count() }} sacolas)
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Empacotamento sem peças individuais (sistema legado) -->
                        <div class="border border-gray-200 rounded-lg p-3 mb-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-bold text-base">{{ $empacotamento->codigo_qr }}</h3>
                                    <p class="text-yellow-600 font-medium text-sm">{{ $empacotamento->status->nome }}</p>
                                    @if($empacotamento->coleta && $empacotamento->coleta->estabelecimento)
                                        <p class="text-gray-900 font-medium text-sm">{{ Str::limit($empacotamento->coleta->estabelecimento->nome_fantasia ?? $empacotamento->coleta->estabelecimento->razao_social, 30) }}</p>
                                    @else
                                        <p class="text-red-600 font-medium text-sm">Estabelecimento não encontrado</p>
                                    @endif
                                    @if($empacotamento->entrega && $empacotamento->entrega->data_saida)
                                        <p class="text-gray-600 text-xs">Saída: {{ $empacotamento->entrega->data_saida->format('d/m/Y H:i') }}</p>
                                    @endif
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⚠️ Empacotamento Legado
                                        </span>
                                    </div>
                                </div>
                                <button onclick="abrirModalEntrega({{ $empacotamento->id }}, '{{ $empacotamento->codigo_qr }}')"
                                        class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 ml-2">
                                    Confirmar Entrega
                                </button>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">🚚</div>
                        <p class="text-gray-500 text-lg">Nenhuma sacola em trânsito</p>
                        <p class="text-gray-400 text-sm">As sacolas aparecerão aqui após confirmarem a saída</p>
                    </div>
                @endforelse
            </div>

            <!-- Entregues Hoje -->
            <div id="content-entregues" class="main-tab-content hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">✅ Entregas Realizadas Hoje</h3>
                    <p class="text-gray-600 text-sm">Histórico completo com assinaturas de recebimento</p>
                </div>
                
                @forelse($empacotamentosEntregues as $empacotamento)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-green-50">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <!-- Informações do Empacotamento -->
                            <div class="lg:col-span-2">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg text-gray-900">{{ $empacotamento->codigo_qr }}</h4>
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium mt-1">
                                            ✅ {{ $empacotamento->status->nome }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Estabelecimento -->
                                @if($empacotamento->coleta && $empacotamento->coleta->estabelecimento)
                                    <div class="mb-2">
                                        <p class="text-gray-700 font-medium">📍 {{ $empacotamento->coleta->estabelecimento->nome_fantasia ?? $empacotamento->coleta->estabelecimento->razao_social }}</p>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <p class="text-red-600 font-medium">📍 Estabelecimento não encontrado</p>
                                    </div>
                                @endif
                                
                                <!-- Informações de Entrega -->
                                @if($empacotamento->entrega)
                                    <div class="space-y-1 text-sm">
                                        <p class="text-gray-600">
                                            <span class="font-medium">🕐 Data/Hora:</span>
                                            {{ $empacotamento->entrega->data_entrega->format('d/m/Y H:i') }}
                                        </p>
                                        @if($empacotamento->entrega->motoristaEntrega)
                                            <p class="text-gray-600">
                                                <span class="font-medium">🚚 Motorista:</span>
                                                {{ $empacotamento->entrega->motoristaEntrega->nome }}
                                            </p>
                                        @endif
                                        @if($empacotamento->entrega->nome_recebedor)
                                            <p class="text-gray-600">
                                                <span class="font-medium">👤 Recebido por:</span>
                                                {{ $empacotamento->entrega->nome_recebedor }}
                                            </p>
                                        @endif
                                        @if($empacotamento->entrega->observacoes)
                                            <p class="text-gray-600">
                                                <span class="font-medium">📝 Observações:</span>
                                                {{ Str::limit($empacotamento->entrega->observacoes, 100) }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Assinatura -->
                            <div class="flex flex-col">
                                <h5 class="font-medium text-gray-700 mb-2">✍️ Assinatura de Recebimento</h5>
                                @if($empacotamento->entrega && $empacotamento->entrega->assinatura_recebedor)
                                    <div class="border-2 border-gray-300 rounded-lg bg-white p-2 flex-1 min-h-[120px] flex items-center justify-center">
                                        <img src="{{ $empacotamento->entrega->assinatura_recebedor }}" 
                                             alt="Assinatura de {{ $empacotamento->entrega->nome_recebedor }}"
                                             class="max-w-full max-h-full object-contain">
                                    </div>
                                    <button onclick="verAssinaturaCompleta('{{ $empacotamento->entrega->assinatura_recebedor }}', '{{ $empacotamento->entrega->nome_recebedor }}', '{{ $empacotamento->codigo_qr }}')"
                                            class="mt-2 text-xs text-blue-600 hover:text-blue-800 underline">
                                        👁️ Ver assinatura completa
                                    </button>
                                @else
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-100 p-4 flex-1 min-h-[120px] flex items-center justify-center">
                                        <p class="text-gray-500 text-sm text-center">
                                            Assinatura não disponível
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📋</div>
                        <p class="text-gray-500 text-lg">Nenhuma entrega completa realizada hoje</p>
                        <p class="text-gray-400 text-sm">As entregas aparecerão aqui após serem confirmadas</p>
                    </div>
                @endforelse

                <!-- Peças entregues individualmente hoje -->
                @if(isset($pecasEntreguesHoje) && $pecasEntreguesHoje->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">📦 Sacolas Entregues Hoje ({{ $pecasEntreguesHoje->count() }})</h3>
                        <p class="text-gray-600 text-sm mb-3">Sacolas entregues individualmente</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($pecasEntreguesHoje as $peca)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-start justify-between mb-1">
                                        <div>
                                            <h5 class="font-semibold text-sm text-gray-900">✅ {{ $peca->tipo->nome ?? 'N/A' }}</h5>
                                            <p class="text-xs text-gray-500">{{ $peca->tipo->categoria ?? '' }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Entregue
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">Qtd:</span> {{ $peca->quantidade }} •
                                        <span class="font-medium">QR:</span> <span class="font-mono">{{ $peca->codigo_qr }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        🏢 {{ $peca->empacotamento->coleta->estabelecimento->razao_social ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        🕐 {{ $peca->data_entrega->format('H:i') }}
                                        @if($peca->nome_recebedor)
                                            • 👤 {{ $peca->nome_recebedor }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Saída - Ação Rápida -->
<div id="modalSaida" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
            <div class="p-4">
                <h3 class="text-base font-bold text-green-900 mb-3">🚚 Confirmar Saída para Entrega</h3>
                <div class="bg-green-50 p-3 rounded-lg mb-3">
                    <p class="text-green-800 text-sm font-medium">QR Code: <span id="codigoSaida" class="font-mono"></span></p>
                    <p class="text-green-700 text-xs" id="estabelecimentoSaida"></p>
                    <p class="text-green-600 text-xs" id="dataSaida"></p>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="fecharModalSaida()"
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button onclick="confirmarSaidaRapida()"
                            class="flex-1 px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        ✅ Confirmar Saída
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Entrega - Compacto -->
<div id="modalEntrega" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
            <div class="p-4">
                <h3 class="text-base font-bold text-gray-900 mb-3">Confirmar Entrega</h3>
                <p class="text-gray-600 mb-3 text-sm">QR: <span id="codigoEntrega" class="font-mono font-bold"></span></p>

                <form id="formEntrega">
                    <input type="hidden" id="empacotamentoId" name="empacotamento_id">

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome de quem recebeu</label>
                        <input type="text" id="nomeRecebedor" name="nome_recebedor" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nome completo">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Assinatura</label>
                        <div class="border border-gray-300 rounded-lg">
                            <canvas id="canvasAssinatura" width="300" height="120" class="w-full cursor-crosshair"></canvas>
                        </div>
                        <div class="flex justify-between mt-1">
                            <button type="button" onclick="limparAssinatura()"
                                    class="text-xs text-gray-600 hover:text-gray-800">
                                Limpar
                            </button>
                            <span class="text-xs text-gray-500">Desenhe acima</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="fecharModalEntrega()"
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Assinatura Completa -->
<div id="modalAssinatura" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">✍️ Assinatura de Recebimento</h3>
                    <button onclick="fecharModalAssinatura()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm text-gray-600"><span class="font-medium">Empacotamento:</span> <span id="assinatura-codigo"></span></p>
                        <p class="text-sm text-gray-600"><span class="font-medium">Recebido por:</span> <span id="assinatura-nome"></span></p>
                    </div>
                    
                    <div class="border-2 border-gray-300 rounded-lg bg-white p-4 text-center">
                        <img id="assinatura-imagem" src="" alt="Assinatura" class="max-w-full h-auto mx-auto" style="max-height: 300px;">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button onclick="fecharModalAssinatura()" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Scanner QR Code -->
<div id="modalScannerQR" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">📱 Scanner QR Code</h3>
                    <button onclick="fecharScannerQR()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4 text-center">
                    <p class="text-gray-600 text-sm mb-2">📱 Posicione o QR Code dentro da área destacada</p>
                    <div class="text-xs text-gray-500 mb-2">
                        ✅ <strong>Empacotamento</strong> (EMP...) ou <strong>Sacola</strong> (PC...)
                    </div>
                    <div class="text-xs text-blue-600 mb-3">
                        💡 <strong>Dicas:</strong> Mantenha estável por 2-3 segundos • Use boa iluminação • Evite reflexos
                    </div>
                    <div class="text-xs text-green-600 mb-3">
                        🚀 <strong>jsQR - Scanner otimizado</strong> - Mais leve e confiável
                    </div>
                    
                    <!-- Status da câmera -->
                    <div id="camera-status" class="mb-3 p-2 rounded text-xs">
                        <div class="flex items-center justify-center">
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Iniciando câmera...
                        </div>
                    </div>
                    
                    <div class="qr-scanner-container">
                        <div id="qr-reader"></div>
                    </div>
                </div>
                
                <div class="text-center space-y-2">
                    <div class="flex gap-2 justify-center flex-wrap">
                        <button onclick="fecharScannerQR()" 
                                class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                            Cancelar
                        </button>
                        <button onclick="solicitarPermissaoCamera()" 
                                class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            🔓 Permitir Câmera
                        </button>
                        <button onclick="testarQRCodeManual()" 
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            ✏️ Testar Manual
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Use "Testar Manual" se a câmera não funcionar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmação Saída por Sacola -->
<div id="modalSaidaSacola" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
            <div class="p-4">
                <h3 class="text-base font-bold text-green-900 mb-3">🚚 Confirmação Automática</h3>
                <div class="bg-green-50 p-3 rounded-lg mb-3">
                    <p class="text-green-800 text-sm font-medium">
                        <span class="block">🏷️ <span id="sacola-tipo"></span></span>
                        <span class="block">📦 QR: <span id="sacola-codigo" class="font-mono"></span></span>
                        <span class="block">🏢 <span id="sacola-estabelecimento"></span></span>
                        <span class="block">📊 Qtd: <span id="sacola-quantidade"></span> peças</span>
                    </p>
                    <div class="text-xs text-blue-600 mt-2 p-2 bg-blue-50 rounded">
                        ⚡ <strong>Confirmação automática</strong> em 3 segundos para agilizar o processo
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="fecharModalSaidaSacola()"
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button onclick="confirmarSaidaSacolaRapida()"
                            class="flex-1 px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        ✅ Confirmar Saída
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Variáveis globais para assinatura
let canvas, ctx, isDrawing = false;

// Variáveis globais para scanner QR
let html5QrcodeScanner = null;
let sacolaParaConfirmacao = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar com a aba "Prontos"
    showMainTab('prontos');

    // Inicializar canvas de assinatura
    canvas = document.getElementById('canvasAssinatura');
    ctx = canvas.getContext('2d');
    
    // Configurar canvas
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    
    // Event listeners para desenhar
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Touch events para mobile
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
});

function showMainTab(tabName) {
    // Esconder todos os conteúdos
    document.querySelectorAll('.main-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover classe ativa de todos os botões
    document.querySelectorAll('.main-tab-button').forEach(button => {
        button.classList.remove('border-green-500', 'text-green-600', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar conteúdo ativo
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Ativar botão com cor apropriada
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    
    if (tabName === 'prontos') {
        activeButton.classList.add('border-green-500', 'text-green-600');
    } else {
        activeButton.classList.add('border-blue-500', 'text-blue-600');
    }
}



// Função para confirmar saída de sacola individual
function confirmarSaidaSacola(codigoQR, tipoPeca, empacotamentoId) {
    if (!confirm(`Confirmar saída da sacola:\n🏷️ ${tipoPeca}\n📦 QR: ${codigoQR}\n\nEsta ação irá marcar apenas esta sacola como "em trânsito".`)) return;
    
    // Confirmar saída apenas desta sacola individual
    fetch('{{ route("motorista.confirmar-saida-sacola") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ codigo_qr: codigoQR })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message || 'Erro ao confirmar saída da sacola');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('❌ Erro ao confirmar saída da sacola');
    });
}

// Função para confirmar saída de todas as sacolas do empacotamento
function confirmarSaidaCompleta(empacotamentoId) {
    if (!confirm('🚚 CONFIRMAR TODAS AS SACOLAS\n\nEsta ação irá:\n✅ Mover TODAS as sacolas para "Em Trânsito"\n✅ Mover o empacotamento completo para "Sacolas em Trânsito"\n\nDeseja continuar?')) return;
    
    // Mostrar loading
    const loadingMsg = mostrarMensagemCarregando("🚚 Confirmando todas as sacolas...");
    
    fetch('{{ route("motorista.confirmar-todas-sacolas") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ empacotamento_id: empacotamentoId })
    })
    .then(response => response.json())
    .then(data => {
        ocultarMensagemCarregando(loadingMsg);
        
        if (data.success) {
            // Tocar som de sucesso e mostrar toast
            playSuccessSound();
            mostrarToastSucesso(data.message);
            
            // Aguardar um pouco para o usuário ver o feedback e depois recarregar
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            alert('❌ ' + (data.message || 'Erro ao confirmar saída de todas as sacolas'));
        }
    })
    .catch(error => {
        ocultarMensagemCarregando(loadingMsg);
        console.error('Erro:', error);
        alert('❌ Erro ao confirmar saída de todas as sacolas');
    });
}

// Função para confirmar saída (sistema legado)
function confirmarSaida(empacotamentoId) {
    if (!confirm('Confirmar saída para entrega?')) return;
    
    fetch('{{ route("motorista.confirmar-saida") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ empacotamento_id: empacotamentoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erro ao confirmar saída');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao confirmar saída');
    });
}

function abrirModalEntrega(empacotamentoId, codigo) {
    document.getElementById('empacotamentoId').value = empacotamentoId;
    document.getElementById('codigoEntrega').textContent = codigo;
    document.getElementById('nomeRecebedor').value = '';
    // Limpar modo de entrega (todas)
    window.modoEntrega = 'todas';
    window.pecasSelecionadasIds = [];
    limparAssinatura();
    document.getElementById('modalEntrega').classList.remove('hidden');
}

function abrirModalEntregaSelecionadas(empacotamentoId, codigo) {
    // Coletar IDs das peças selecionadas
    const checkboxes = document.querySelectorAll(`.peca-checkbox[data-empacotamento-id="${empacotamentoId}"]:checked`);
    const pecasIds = Array.from(checkboxes).map(cb => cb.dataset.pecaId);
    
    if (pecasIds.length === 0) {
        alert('Selecione pelo menos uma sacola para entregar.');
        return;
    }
    
    window.modoEntrega = 'selecionadas';
    window.pecasSelecionadasIds = pecasIds;
    
    document.getElementById('empacotamentoId').value = empacotamentoId;
    document.getElementById('codigoEntrega').textContent = codigo + ` (${pecasIds.length} sacola${pecasIds.length > 1 ? 's' : ''})`;
    document.getElementById('nomeRecebedor').value = '';
    limparAssinatura();
    document.getElementById('modalEntrega').classList.remove('hidden');
}

function selecionarTodasPecas(empacotamentoId, checked) {
    const checkboxes = document.querySelectorAll(`.peca-checkbox[data-empacotamento-id="${empacotamentoId}"]`);
    checkboxes.forEach(cb => cb.checked = checked);
    atualizarSelecaoEntrega(empacotamentoId);
}

function atualizarSelecaoEntrega(empacotamentoId) {
    const checkboxes = document.querySelectorAll(`.peca-checkbox[data-empacotamento-id="${empacotamentoId}"]`);
    const selecionadas = document.querySelectorAll(`.peca-checkbox[data-empacotamento-id="${empacotamentoId}"]:checked`);
    const total = checkboxes.length;
    const totalSelecionadas = selecionadas.length;
    
    // Atualizar contador
    const contador = document.getElementById(`contador-selecionadas-${empacotamentoId}`);
    if (contador) contador.textContent = `${totalSelecionadas} de ${total} selecionadas`;
    
    // Atualizar total no botão
    const totalSpan = document.getElementById(`total-selecionadas-${empacotamentoId}`);
    if (totalSpan) totalSpan.textContent = totalSelecionadas;
    
    // Mostrar/esconder botão de entregar selecionadas
    const btnSelecionadas = document.getElementById(`btn-entregar-selecionadas-${empacotamentoId}`);
    if (btnSelecionadas) {
        if (totalSelecionadas > 0 && totalSelecionadas < total) {
            btnSelecionadas.classList.remove('hidden');
        } else {
            btnSelecionadas.classList.add('hidden');
        }
    }
    
    // Destacar peças selecionadas
    checkboxes.forEach(cb => {
        const card = cb.closest('.peca-entrega-item');
        if (card) {
            if (cb.checked) {
                card.classList.add('border-green-400', 'bg-green-50');
                card.classList.remove('border-yellow-200');
            } else {
                card.classList.remove('border-green-400', 'bg-green-50');
                card.classList.add('border-yellow-200');
            }
        }
    });
}

function fecharModalEntrega() {
    document.getElementById('modalEntrega').classList.add('hidden');
}

// Funções para modal de saída rápida
let empacotamentoParaSaida = null;

function abrirModalSaida(empacotamento) {
    empacotamentoParaSaida = empacotamento;
    document.getElementById('codigoSaida').textContent = empacotamento.codigo_qr;

    // Mostrar informações do estabelecimento
    if (empacotamento.coleta && empacotamento.coleta.estabelecimento) {
        const estabelecimento = empacotamento.coleta.estabelecimento;
        document.getElementById('estabelecimentoSaida').textContent =
            `📍 ${estabelecimento.nome_fantasia || estabelecimento.razao_social}`;
    } else {
        document.getElementById('estabelecimentoSaida').textContent = '📍 Estabelecimento não encontrado';
    }

    // Mostrar data de empacotamento
    const dataEmpacotamento = new Date(empacotamento.data_empacotamento).toLocaleString('pt-BR');
    document.getElementById('dataSaida').textContent = `📦 Empacotado em: ${dataEmpacotamento}`;

    document.getElementById('modalSaida').classList.remove('hidden');
}

function fecharModalSaida() {
    document.getElementById('modalSaida').classList.add('hidden');
    empacotamentoParaSaida = null;
}

function confirmarSaidaRapida() {
    if (!empacotamentoParaSaida) return;

    fetch('{{ route("motorista.confirmar-saida") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ empacotamento_id: empacotamentoParaSaida.id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            fecharModalSaida();
            location.reload();
        } else {
            alert('❌ Erro ao confirmar saída');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('❌ Erro ao confirmar saída');
    });
}

// Funções de assinatura
function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing = false;
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                     e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function limparAssinatura() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Submit do formulário de entrega
document.getElementById('formEntrega').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const nomeRecebedor = document.getElementById('nomeRecebedor').value.trim();
    if (!nomeRecebedor) {
        alert('Digite o nome de quem recebeu');
        return;
    }
    
    // Verificar se há assinatura
    const imageData = canvas.toDataURL();
    const isCanvasBlank = !ctx.getImageData(0, 0, canvas.width, canvas.height).data.some(channel => channel !== 0);
    
    if (isCanvasBlank) {
        alert('Por favor, faça a assinatura');
        return;
    }
    
    const empacotamentoId = document.getElementById('empacotamentoId').value;
    
    // Verificar se é entrega selecionada ou todas
    let url, bodyData;
    
    if (window.modoEntrega === 'selecionadas' && window.pecasSelecionadasIds && window.pecasSelecionadasIds.length > 0) {
        url = '{{ route("entrega.confirmar-entrega-selecionadas") }}';
        bodyData = {
            empacotamento_id: empacotamentoId,
            pecas_ids: window.pecasSelecionadasIds,
            nome_recebedor: nomeRecebedor,
            assinatura: imageData
        };
    } else {
        url = '{{ route("motorista.confirmar-entrega") }}';
        bodyData = {
            empacotamento_id: empacotamentoId,
            nome_recebedor: nomeRecebedor,
            assinatura_recebedor: imageData
        };
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(bodyData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fecharModalEntrega();
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro ao confirmar entrega'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao confirmar entrega');
    });
});

// Funções para modal de assinatura
function verAssinaturaCompleta(assinatura, nome, codigo) {
    document.getElementById('assinatura-imagem').src = assinatura;
    document.getElementById('assinatura-nome').textContent = nome;
    document.getElementById('assinatura-codigo').textContent = codigo;
    document.getElementById('modalAssinatura').classList.remove('hidden');
}

function fecharModalAssinatura() {
    document.getElementById('modalAssinatura').classList.add('hidden');
}

// ============ FUNÇÕES SCANNER QR CODE COM ZXING ============

// Variáveis globais do ZXing
let codeReader = null;
let selectedDeviceId = null;

function abrirScannerQR() {
    // Reset contador de tentativas
    window.scanAttempts = 0;
    
    document.getElementById('modalScannerQR').classList.remove('hidden');
    atualizarStatusCamera('loading', 'Iniciando câmera...');
    iniciarScanner();
}

function fecharScannerQR() {
    pararScanner();
    document.getElementById('modalScannerQR').classList.add('hidden');
}

function iniciarScanner() {
    console.log("🚀 Iniciando scanner com getUserMedia nativo...");
    
    try {
        // Parar scanner anterior se existir
        pararScanner();
        
        // Verificar se getUserMedia está disponível
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('getUserMedia não suportado neste navegador');
        }
        
        // Limpar área do scanner
        const previewElement = document.getElementById('qr-reader');
        previewElement.innerHTML = '<video id="qr-video" autoplay playsinline></video>';
        
        const video = document.getElementById('qr-video');
        video.style.width = '100%';
        video.style.height = '100%';
        video.style.objectFit = 'cover';
        
        // Configurações da câmera - tentar traseira primeiro
        const constraints = {
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
        
        console.log("📱 Solicitando acesso à câmera...");
        
        navigator.mediaDevices.getUserMedia(constraints)
            .then(stream => {
                console.log("✅ Acesso à câmera concedido");
                
                video.srcObject = stream;
                window.currentStream = stream;
                
                video.addEventListener('loadedmetadata', () => {
                    console.log("📹 Vídeo carregado, iniciando detecção jsQR...");
                    atualizarStatusCamera("success", "📹 Câmera ativa - Posicione o QR Code");
                    
                    // Iniciar loop de detecção
                    iniciarLoopDeteccao(video);
                });
                
            })
            .catch(err => {
                console.error("❌ Erro ao acessar câmera traseira:", err);
                
                // Fallback para câmera frontal
                console.log("🔄 Tentando câmera frontal...");
                const constraintsFrontal = {
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };
                
                navigator.mediaDevices.getUserMedia(constraintsFrontal)
                    .then(stream => {
                        console.log("✅ Câmera frontal funcionando");
                        
                        video.srcObject = stream;
                        window.currentStream = stream;
                        
                        video.addEventListener('loadedmetadata', () => {
                            atualizarStatusCamera("warning", "📱 Câmera frontal ativa");
                            iniciarLoopDeteccao(video);
                        });
                    })
                    .catch(err2 => {
                        console.error("❌ Erro também na câmera frontal:", err2);
                        atualizarStatusCamera("error", "❌ Erro ao acessar câmera");
                        
                        // Mostrar mensagem específica baseada no erro
                        let mensagem = "❌ Erro ao acessar câmera.\n\n";
                        
                        if (err2.name === 'NotAllowedError') {
                            mensagem += "🔒 Acesso negado.\n• Clique no ícone da câmera na barra de endereços\n• Permita o acesso à câmera\n• Recarregue a página";
                        } else if (err2.name === 'NotFoundError') {
                            mensagem += "📱 Nenhuma câmera encontrada.\n• Verifique se seu dispositivo tem câmera\n• Use 'Testar Manual'";
                        } else {
                            mensagem += "Possíveis soluções:\n• Permita acesso à câmera\n• Feche outros apps que usam câmera\n• Recarregue a página\n• Use 'Testar Manual'";
                        }
                        
                        alert(mensagem);
                        fecharScannerQR();
                    });
            });
            
    } catch (error) {
        console.error("❌ Erro ao inicializar scanner:", error);
        atualizarStatusCamera("error", "❌ Erro de inicialização");
        alert("❌ Erro ao inicializar scanner.\n\nSeu navegador pode não suportar esta funcionalidade.\nUse 'Testar Manual'.");
        fecharScannerQR();
    }
}

function iniciarLoopDeteccao(video) {
    if (!video) return;
    
    console.log("🔄 Iniciando loop de detecção jsQR...");
    
    // Criar canvas para capturar frames
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    let isScanning = true;
    window.scanningActive = true;
    let frameCount = 0;
    
    function detectarFrame() {
        if (!isScanning || !window.scanningActive) return;
        
        try {
            // Aguardar o vídeo estar pronto
            if (video.readyState !== video.HAVE_ENOUGH_DATA) {
                requestAnimationFrame(detectarFrame);
                return;
            }
            
            // Ajustar canvas ao tamanho do vídeo
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Capturar frame atual do vídeo
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Obter dados da imagem
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            // Tentar decodificar com jsQR
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            
            if (code) {
                console.log("✅ QR Code detectado com jsQR:", code.data);
                isScanning = false;
                window.scanningActive = false;
                onScanSuccess(code.data, code);
                return;
            }
            
            // Feedback visual a cada 60 frames (~2 segundos a 30fps)
            frameCount++;
            if (frameCount % 60 === 0) {
                console.log(`🔍 Escaneando... frame ${frameCount}`);
                atualizarStatusCamera("warning", `📹 Escaneando... (${Math.floor(frameCount/30)}s)`);
            }
                
        } catch (error) {
            console.error("❌ Erro no loop jsQR:", error);
        }
        
        // Continuar o loop se ainda estiver escaneando
        if (isScanning && window.scanningActive) {
            requestAnimationFrame(detectarFrame);
        }
    }
    
    // Aguardar vídeo carregar e iniciar
    video.addEventListener('loadeddata', () => {
        console.log("📹 Vídeo carregado, iniciando detecção jsQR...");
        detectarFrame();
    });
    
    // Se o vídeo já estiver carregado
    if (video.readyState >= 2) {
        detectarFrame();
    }
}

function pararScanner() {
    console.log("🛑 Parando scanner...");
    
    try {
        // Parar loop de detecção
        window.scanningActive = false;
        
        // Parar stream de vídeo
        if (window.currentStream) {
            window.currentStream.getTracks().forEach(track => {
                track.stop();
                console.log("📹 Track parado:", track.kind);
            });
            window.currentStream = null;
        }
        
        // Limpar variáveis globais
        codeReader = null;
        
        // Limpar elemento de vídeo
        const previewElement = document.getElementById('qr-reader');
        if (previewElement) {
            previewElement.innerHTML = '';
        }
        
        console.log("✅ Scanner parado completamente");
        
    } catch (error) {
        console.error("❌ Erro ao parar scanner:", error);
    }
}

function onScanSuccess(decodedText, decodedResult) {
    console.log(`✅ QR Code detectado com sucesso: ${decodedText}`);
    console.log("Resultado completo:", decodedResult);
    
    // Parar scanner imediatamente
    console.log("Parando scanner...");
    pararScanner();
    fecharScannerQR();
    
    // Processar o QR Code
    console.log("Processando QR Code...");
    processarQRCode(decodedText);
}

function onScanFailure(error) {
    // Log apenas erros importantes, não os de "não encontrado"
    if (error && !error.includes("No QR code found") && !error.includes("QR code parse error") && !error.includes("NotFoundException")) {
        console.warn("⚠️ Erro no scanner:", error);
    }
    
    // Contador de tentativas para feedback visual
    if (!window.scanAttempts) window.scanAttempts = 0;
    window.scanAttempts++;
    
    // A cada 50 tentativas, dar feedback
    if (window.scanAttempts % 50 === 0) {
        console.log(`🔍 ${window.scanAttempts} tentativas de scan... Continue posicionando o QR Code`);
        atualizarStatusCamera("warning", `📹 Tentativa ${window.scanAttempts} - Continue tentando`);
    }
}

function processarQRCode(codigo) {
    console.log("🔍 PROCESSANDO QR CODE:", codigo);
    
    try {
        // Mostrar loading
        const loadingMsg = mostrarMensagemCarregando("🔍 Identificando QR Code...");
        console.log("Loading message criado:", loadingMsg);
        
        // Detectar tipo de QR code baseado no padrão
        if (codigo.startsWith('EMP')) {
            console.log("✅ QR Code identificado como EMPACOTAMENTO");
            console.log("Chamando buscarEmpacotamento...");
            buscarEmpacotamento(codigo, loadingMsg);
        } else if (codigo.startsWith('PC')) {
            console.log("✅ QR Code identificado como SACOLA INDIVIDUAL");
            console.log("Chamando buscarSacola...");
            buscarSacola(codigo, loadingMsg);
        } else {
            console.log("⚠️ Tipo de QR Code não reconhecido:", codigo);
            console.log("Tentando identificar automaticamente...");
            tentarIdentificarQRCode(codigo, loadingMsg);
        }
    } catch (error) {
        console.error("❌ ERRO ao processar QR Code:", error);
        alert("Erro ao processar QR Code: " + error.message);
    }
}

function buscarEmpacotamento(codigo, loadingMsg) {
    console.log("📦 BUSCANDO EMPACOTAMENTO:", codigo);
    
    try {
        // Atualizar mensagem de loading
        if (loadingMsg && loadingMsg.querySelector('svg') && loadingMsg.querySelector('svg').nextSibling) {
            loadingMsg.querySelector('svg').nextSibling.textContent = '📦 Buscando empacotamento...';
        }
        
        console.log("Fazendo requisição para:", '{{ route("motorista.buscar-empacotamento") }}');
        
        fetch('{{ route("motorista.buscar-empacotamento") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ codigo: codigo })
        })
    .then(response => {
        console.log("📡 Resposta recebida:", response);
        return response.json();
    })
    .then(data => {
        console.log("📊 Dados recebidos:", data);
        ocultarMensagemCarregando(loadingMsg);
        
        if (data.success) {
            console.log("✅ Empacotamento encontrado, abrindo modal...");
            abrirModalSaidaEmpacotamento(data.empacotamento);
        } else {
            console.log("❌ Erro na resposta:", data.message);
            alert(data.message || '❌ Empacotamento não encontrado');
        }
    })
    .catch(error => {
        console.error('❌ ERRO na requisição:', error);
        ocultarMensagemCarregando(loadingMsg);
        alert('❌ Erro ao buscar empacotamento. Tente novamente.');
    });
    
    } catch (error) {
        console.error("❌ ERRO em buscarEmpacotamento:", error);
        ocultarMensagemCarregando(loadingMsg);
        alert("Erro interno: " + error.message);
    }
}

function buscarSacola(codigo, loadingMsg) {
    // Atualizar mensagem de loading
    loadingMsg.querySelector('svg').nextSibling.textContent = '🏷️ Buscando sacola...';
    
    fetch('{{ route("motorista.buscar-sacola") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ codigo: codigo })
    })
    .then(response => response.json())
    .then(data => {
        ocultarMensagemCarregando(loadingMsg);
        
        if (data.success) {
            // Sacola encontrada, mostrar modal de confirmação
            abrirModalSaidaSacola(data.sacola);
        } else {
            alert(data.message || '❌ Sacola não encontrada');
        }
    })
    .catch(error => {
        ocultarMensagemCarregando(loadingMsg);
        console.error('Erro:', error);
        alert('❌ Erro ao buscar sacola. Tente novamente.');
    });
}

function tentarIdentificarQRCode(codigo, loadingMsg) {
    // Tentar primeiro como sacola, depois como empacotamento
    loadingMsg.querySelector('svg').nextSibling.textContent = '🔍 Identificando tipo...';
    
    // Primeiro, tentar como sacola
    fetch('{{ route("motorista.buscar-sacola") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ codigo: codigo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            ocultarMensagemCarregando(loadingMsg);
            abrirModalSaidaSacola(data.sacola);
        } else {
            // Se não encontrou como sacola, tentar como empacotamento
            return fetch('{{ route("motorista.buscar-empacotamento") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ codigo: codigo })
            });
        }
    })
    .then(response => {
        if (response) {
            return response.json();
        }
        return null;
    })
    .then(data => {
        ocultarMensagemCarregando(loadingMsg);
        
        if (data && data.success) {
            abrirModalSaidaEmpacotamento(data.empacotamento);
        } else {
            alert('❌ QR Code não reconhecido!\n\nVerifique se é um QR Code válido de:\n• Sacola individual (PC...)\n• Empacotamento (EMP...)');
        }
    })
    .catch(error => {
        ocultarMensagemCarregando(loadingMsg);
        console.error('Erro:', error);
        alert('❌ Erro ao identificar QR Code. Tente novamente.');
    });
}

// ============ FUNÇÕES SAÍDA POR EMPACOTAMENTO ============

function abrirModalSaidaEmpacotamento(empacotamento) {
    console.log("🎯 ABRINDO MODAL DE SAÍDA EMPACOTAMENTO:", empacotamento);
    
    try {
        // Usar o modal existente de saída, mas adaptado para empacotamento completo
        const codigoElement = document.getElementById('codigoSaida');
        if (codigoElement) {
            codigoElement.textContent = empacotamento.codigo_qr;
            console.log("✅ Código definido:", empacotamento.codigo_qr);
        } else {
            console.error("❌ Elemento 'codigoSaida' não encontrado!");
        }

    // Mostrar informações do estabelecimento
    if (empacotamento.coleta && empacotamento.coleta.estabelecimento) {
        const estabelecimento = empacotamento.coleta.estabelecimento;
        document.getElementById('estabelecimentoSaida').textContent =
            `📍 ${estabelecimento.nome_fantasia || estabelecimento.razao_social}`;
    } else {
        document.getElementById('estabelecimentoSaida').textContent = '📍 Estabelecimento não encontrado';
    }

    // Mostrar data de empacotamento
    const dataEmpacotamento = new Date(empacotamento.data_empacotamento).toLocaleString('pt-BR');
    document.getElementById('dataSaida').textContent = `📦 Empacotado em: ${dataEmpacotamento}`;

    // Guardar referência para confirmação
    empacotamentoParaSaida = empacotamento;
    
    console.log("🎯 Tentando abrir modal 'modalSaida'...");
    const modal = document.getElementById('modalSaida');
    if (modal) {
        modal.classList.remove('hidden');
        console.log("✅ Modal aberto com sucesso!");
    } else {
        console.error("❌ Modal 'modalSaida' não encontrado!");
    }
    
    } catch (error) {
        console.error("❌ ERRO em abrirModalSaidaEmpacotamento:", error);
        alert("Erro ao abrir modal: " + error.message);
    }
}

// ============ FUNÇÕES AUXILIARES ============

function playSuccessSound() {
    // Criar um tom de sucesso usando Web Audio API
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        // Tom de sucesso: duas notas
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
        
        console.log("🔊 Som de sucesso reproduzido");
    } catch (error) {
        console.log("🔇 Som não suportado pelo navegador");
    }
}

function mostrarToastSucesso(mensagem) {
    // Criar elemento toast
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
    toast.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">${mensagem}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remover após 3 segundos
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// ============ FUNÇÕES SAÍDA POR SACOLA ============

function abrirModalSaidaSacola(sacola) {
    sacolaParaConfirmacao = sacola;
    
    // Preencher dados no modal
    document.getElementById('sacola-tipo').textContent = sacola.tipo.nome;
    document.getElementById('sacola-codigo').textContent = sacola.codigo_qr;
    document.getElementById('sacola-estabelecimento').textContent = 
        sacola.empacotamento.coleta.estabelecimento.nome_fantasia || 
        sacola.empacotamento.coleta.estabelecimento.razao_social;
    document.getElementById('sacola-quantidade').textContent = sacola.quantidade;
    
    document.getElementById('modalSaidaSacola').classList.remove('hidden');
    
    // 🚀 CONFIRMAÇÃO AUTOMÁTICA em 3 segundos
    console.log("⏱️ Iniciando confirmação automática em 3 segundos...");
    
    let countdown = 3;
    const botaoConfirmar = document.querySelector('#modalSaidaSacola button[onclick="confirmarSaidaSacolaRapida()"]');
    const textoOriginal = botaoConfirmar.textContent;
    
    // Atualizar botão com countdown
    const interval = setInterval(() => {
        botaoConfirmar.textContent = `⏱️ Confirmando em ${countdown}s...`;
        countdown--;
        
        if (countdown < 0) {
            clearInterval(interval);
            botaoConfirmar.textContent = "🚀 Confirmando...";
            
            // Confirmar automaticamente
            console.log("✅ Confirmação automática executada!");
            confirmarSaidaSacolaRapida();
        }
    }, 1000);
    
    // Guardar interval para poder cancelar se necessário
    window.currentCountdown = interval;
    window.originalButtonText = textoOriginal;
}

function fecharModalSaidaSacola() {
    // Cancelar countdown se existir
    if (window.currentCountdown) {
        clearInterval(window.currentCountdown);
        window.currentCountdown = null;
        
        // Restaurar texto original do botão
        const botaoConfirmar = document.querySelector('#modalSaidaSacola button[onclick="confirmarSaidaSacolaRapida()"]');
        if (botaoConfirmar && window.originalButtonText) {
            botaoConfirmar.textContent = window.originalButtonText;
        }
    }
    
    document.getElementById('modalSaidaSacola').classList.add('hidden');
    sacolaParaConfirmacao = null;
}

function confirmarSaidaSacolaRapida() {
    if (!sacolaParaConfirmacao) return;

    const loadingMsg = mostrarMensagemCarregando("🚚 Confirmando saída...");

    fetch('{{ route("motorista.confirmar-saida-sacola") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ codigo_qr: sacolaParaConfirmacao.codigo_qr })
    })
    .then(response => response.json())
    .then(data => {
        ocultarMensagemCarregando(loadingMsg);
        
        if (data.success) {
            // Reproduzir som de sucesso
            playSuccessSound();
            
            // Mostrar mensagem de sucesso sem alert (mais elegante)
            console.log('✅ ' + data.message);
            
            // Fechar modal imediatamente
            fecharModalSaidaSacola();
            
            // Mostrar toast/notificação visual rápida
            mostrarToastSucesso(data.message);
            
            // Recarregar página após 1 segundo para ver mudanças
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('❌ ' + (data.message || 'Erro ao confirmar saída'));
        }
    })
    .catch(error => {
        ocultarMensagemCarregando(loadingMsg);
        console.error('Erro:', error);
        alert('❌ Erro ao confirmar saída. Tente novamente.');
    });
}

// ============ FUNÇÕES AUXILIARES ============

function mostrarMensagemCarregando(texto) {
    const div = document.createElement('div');
    div.id = 'loading-message';
    div.className = 'fixed top-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
    div.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        ${texto}
    `;
    document.body.appendChild(div);
    return div;
}

function ocultarMensagemCarregando(elemento) {
    if (elemento && elemento.parentNode) {
        elemento.parentNode.removeChild(elemento);
    }
}

// ============ FUNÇÕES DE STATUS DA CÂMERA ============

function atualizarStatusCamera(tipo, mensagem) {
    const statusEl = document.getElementById('camera-status');
    if (!statusEl) return;
    
    let classes = 'mb-3 p-2 rounded text-xs ';
    let icon = '';
    
    switch(tipo) {
        case 'loading':
            classes += 'bg-blue-100 text-blue-800';
            icon = `<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>`;
            break;
        case 'success':
            classes += 'bg-green-100 text-green-800';
            icon = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>`;
            break;
        case 'warning':
            classes += 'bg-yellow-100 text-yellow-800';
            icon = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>`;
            break;
        case 'error':
            classes += 'bg-red-100 text-red-800';
            icon = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>`;
            break;
    }
    
    statusEl.className = classes;
    statusEl.innerHTML = `
        <div class="flex items-center justify-center">
            ${icon}
            ${mensagem}
        </div>
    `;
}

// ============ FUNÇÃO PARA SOLICITAR PERMISSÃO ============

function solicitarPermissaoCamera() {
    console.log("🔓 Solicitando permissão da câmera...");
    atualizarStatusCamera("loading", "🔓 Solicitando permissão...");
    
    // Tentar solicitar permissão explicitamente
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment',
            width: { ideal: 1280 },
            height: { ideal: 720 }
        } 
    })
    .then(stream => {
        console.log("✅ Permissão concedida!");
        atualizarStatusCamera("success", "✅ Permissão concedida! Reiniciando...");
        
        // Parar o stream temporário
        stream.getTracks().forEach(track => track.stop());
        
        // Reiniciar scanner
        setTimeout(() => {
            iniciarScanner();
        }, 1000);
    })
    .catch(err => {
        console.error("❌ Permissão negada:", err);
        
        let mensagem = "❌ Não foi possível obter permissão.\n\n";
        
        if (err.name === 'NotAllowedError') {
            mensagem += "🔒 Acesso negado pelo usuário.\n\n";
            mensagem += "Para permitir:\n";
            mensagem += "1. Clique no ícone 🔒 ou 📹 na barra de endereços\n";
            mensagem += "2. Selecione 'Permitir' para câmera\n";
            mensagem += "3. Recarregue a página\n\n";
            mensagem += "Ou use 'Testar Manual' como alternativa.";
        } else if (err.name === 'NotFoundError') {
            mensagem += "📱 Nenhuma câmera encontrada no dispositivo.";
        } else {
            mensagem += "Erro: " + err.message;
        }
        
        atualizarStatusCamera("error", "❌ Permissão negada");
        alert(mensagem);
    });
}

// ============ FUNÇÃO PARA TESTE MANUAL ============

function testarQRCodeManual() {
    const codigo = prompt("Digite o código QR para testar:\n(Ex: EMP3KXG10Z ou PC...)");
    if (codigo && codigo.trim()) {
        console.log("Teste manual com código:", codigo.trim());
        processarQRCode(codigo.trim());
    }
}
</script>
@endpush
