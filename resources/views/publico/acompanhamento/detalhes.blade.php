@extends('layouts.public')

@section('title', 'Detalhes da Coleta ' . $coleta->numero_coleta)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header - Mais compacto -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $coleta->numero_coleta }}</h1>
                    <p class="text-gray-600 text-sm">{{ $coleta->estabelecimento->razao_social }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          style="background-color: {{ $coleta->status->cor }}20; color: {{ $coleta->status->cor }};">
                        {{ $progresso['status_atual'] }}
                    </span>
                    <button onclick="history.back()" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md transition-colors duration-200 text-sm">
                        ← Voltar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Coluna Principal - Timeline -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Progress Overview - Mais compacto -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">📈 Progresso da Coleta</h2>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                        <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-1000" 
                             style="width: {{ $progresso['porcentagem'] }}%"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-600 mb-4">
                        <span>Início</span>
                        <span class="font-medium">{{ $progresso['porcentagem'] }}% Concluído</span>
                        <span>Entregue</span>
                    </div>

                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-900">Status Atual</p>
                        <p class="text-lg font-bold" style="color: {{ $coleta->status->cor }};">
                            {{ $progresso['status_atual'] }}
                        </p>
                    </div>
                </div>

                <!-- Timeline - Mais compacto -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Histórico da Coleta</h2>
                    
                    <div class="space-y-4">
                        @foreach($progresso['etapas'] as $index => $etapa)
                            <div class="flex items-start">
                                <!-- Timeline Icon - Menor -->
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm {{ $etapa['concluida'] ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        @switch($etapa['icone'])
                                            @case('calendar')
                                                📅
                                                @break
                                            @case('truck')
                                                🚚
                                                @break
                                            @case('check-circle')
                                                ✅
                                                @break
                                            @case('scale')
                                                ⚖️
                                                @break
                                            @case('package')
                                                📦
                                                @break
                                            @case('clock')
                                                ⏰
                                                @break
                                            @case('check')
                                                ✅
                                                @break
                                            @default
                                                📋
                                        @endswitch
                                    </div>
                                </div>

                                <!-- Timeline Content - Texto menor -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="text-sm font-medium {{ $etapa['concluida'] ? 'text-gray-900' : 'text-gray-500' }}">
                                            {{ $etapa['titulo'] }}
                                        </h3>
                                        @if($etapa['concluida'] && $etapa['data'])
                                            <span class="text-xs text-gray-500">
                                                {{ $etapa['data']->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs {{ $etapa['concluida'] ? 'text-gray-600' : 'text-gray-400' }}">
                                        {{ $etapa['descricao'] }}
                                    </p>
                                </div>
                            </div>

                            @if(!$loop->last)
                                <div class="ml-4 w-px h-4 bg-gray-300"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar - Informações -->
            <div class="space-y-4">
                <!-- Informações do Estabelecimento - Mais compacto -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">🏢 Estabelecimento</h3>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-gray-500">Razão Social:</span>
                            <p class="font-medium text-gray-900">{{ $coleta->estabelecimento->razao_social }}</p>
                        </div>
                        @if($coleta->estabelecimento->nome_fantasia)
                            <div>
                                <span class="text-gray-500">Nome Fantasia:</span>
                                <p class="font-medium text-gray-900">{{ $coleta->estabelecimento->nome_fantasia }}</p>
                            </div>
                        @endif
                        <div>
                            <span class="text-gray-500">CNPJ:</span>
                            <p class="font-medium text-gray-900">{{ $coleta->estabelecimento->cnpj_formatado ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Endereço:</span>
                            <p class="font-medium text-gray-900">
                                {{ $coleta->estabelecimento->endereco }}<br>
                                {{ $coleta->estabelecimento->cidade }} - {{ $coleta->estabelecimento->estado }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informações da Coleta - Mais compacto -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">📋 Dados da Coleta</h3>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-gray-500">Número:</span>
                            <p class="font-medium text-gray-900">{{ $coleta->numero_coleta }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Data de Criação:</span>
                            <p class="font-medium text-gray-900">{{ $coleta->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Status Atual:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1"
                                  style="background-color: {{ $coleta->status->cor }}20; color: {{ $coleta->status->cor }};">
                                {{ $coleta->status->nome }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Peças da Coleta - Mais compacto -->
                @if($coleta->pecas->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">👕 Peças Coletadas</h3>
                        <div class="space-y-2">
                            @foreach($coleta->pecas as $peca)
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
                                    <div>
                                        <p class="font-medium text-gray-900 text-xs">{{ $peca->tipo->nome }}</p>
                                        <p class="text-xs text-gray-600">{{ $peca->quantidade }} peças</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900 text-xs">{{ number_format($peca->peso, 2, ',', '.') }} kg</p>
                                        <p class="text-xs text-gray-500">R$ {{ number_format($peca->valor_total, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between items-center font-semibold text-gray-900 text-xs">
                                    <span>Total:</span>
                                    <div class="text-right">
                                        <p>{{ $coleta->pecas->sum('quantidade') }} peças</p>
                                        <p>{{ number_format($coleta->pecas->sum('peso'), 2, ',', '.') }} kg</p>
                                        <p class="text-blue-600">R$ {{ number_format($coleta->pecas->sum('valor_total'), 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Pesagem - Mais compacto -->
                @if($coleta->pesagens->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">⚖️ Pesagem</h3>
                        @foreach($coleta->pesagens as $pesagem)
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-500">Peso Total:</span>
                                    <p class="font-medium text-gray-900">{{ number_format($pesagem->peso, 2, ',', '.') }} kg</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Quantidade:</span>
                                    <p class="font-medium text-gray-900">{{ $pesagem->quantidade }} peças</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Data da Pesagem:</span>
                                    <p class="font-medium text-gray-900">{{ $pesagem->data_pesagem->format('d/m/Y H:i') }}</p>
                                </div>
                                @if($pesagem->usuario)
                                    <div>
                                        <span class="text-gray-500">Responsável:</span>
                                        <p class="font-medium text-gray-900">{{ $pesagem->usuario->nome }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Empacotamento - Mais compacto -->
                @if($coleta->empacotamentos->isNotEmpty())
                    @php $empacotamento = $coleta->empacotamentos->first(); @endphp
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">📦 Empacotamento</h3>
                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="text-gray-500">Código QR:</span>
                                <p class="font-medium text-gray-900">{{ $empacotamento->codigo_qr }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Data:</span>
                                <p class="font-medium text-gray-900">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1"
                                      style="background-color: {{ $empacotamento->status->cor }}20; color: {{ $empacotamento->status->cor }};">
                                    {{ $empacotamento->status->nome }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Entrega - Mais compacto -->
                    @if($empacotamento->entrega)
                        <div class="bg-white rounded-lg shadow-sm border p-4">
                            <h3 class="text-base font-semibold text-gray-900 mb-3">🚚 Entrega</h3>
                            <div class="space-y-2 text-xs">
                                @if($empacotamento->entrega->data_saida)
                                    <div>
                                        <span class="text-gray-500">Saída:</span>
                                        <p class="font-medium text-gray-900">{{ $empacotamento->entrega->data_saida->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endif
                                @if($empacotamento->entrega->motoristaSaida)
                                    <div>
                                        <span class="text-gray-500">Motorista:</span>
                                        <p class="font-medium text-gray-900">{{ $empacotamento->entrega->motoristaSaida->nome }}</p>
                                    </div>
                                @endif
                                @if($empacotamento->entrega->data_entrega)
                                    <div>
                                        <span class="text-gray-500">Data da Entrega:</span>
                                        <p class="font-medium text-gray-900">{{ $empacotamento->entrega->data_entrega->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endif
                                @if($empacotamento->entrega->nome_recebedor)
                                    <div>
                                        <span class="text-gray-500">Recebido por:</span>
                                        <p class="font-medium text-gray-900">{{ $empacotamento->entrega->nome_recebedor }}</p>
                                    </div>
                                @endif
                                @if($empacotamento->entrega->assinatura_recebedor)
                                    <div>
                                        <span class="text-gray-500">Assinatura:</span>
                                        <div class="mt-2">
                                            <button onclick="visualizarAssinatura('{{ $empacotamento->entrega->assinatura_recebedor }}', '{{ $empacotamento->entrega->nome_recebedor ?? 'N/A' }}')" 
                                                    class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                                Ver Assinatura
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Animação da barra de progresso ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.querySelector('.bg-gradient-to-r');
        if (progressBar) {
            progressBar.style.width = '0%';
            setTimeout(() => {
                progressBar.style.width = '{{ $progresso["porcentagem"] }}%';
            }, 500);
        }
    });

    // Função para visualizar assinatura
    function visualizarAssinatura(assinatura, nomeRecebedor) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Assinatura do Recebedor</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Recebido por:</p>
                            <p class="font-semibold text-gray-900">${nomeRecebedor}</p>
                        </div>
                        
                        <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50">
                            <img src="${assinatura.startsWith('data:') ? assinatura : '/storage/' + assinatura}" 
                                 alt="Assinatura do recebedor" 
                                 class="max-w-full max-h-32 mx-auto object-contain">
                        </div>
                        
                        <button onclick="this.closest('.fixed').remove()" 
                                class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Fechar com ESC
        modal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                modal.remove();
            }
        });

        // Fechar clicando fora
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
</script>
@endpush
@endsection

