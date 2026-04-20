@extends('layouts.app')

@section('title', 'QR Code - ' . $empacotamento->codigo_qr)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                QR Code - {{ $empacotamento->codigo_qr }}
            </h1>
            <p class="text-sm text-gray-600">Código QR para rastreamento do empacotamento</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir
            </button>
            <a href="{{ route('empacotamento.show', $empacotamento->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- QR Code para Impressão -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 print:shadow-none print:border-none">
        <div class="p-8 text-center">
            <!-- Cabeçalho da Empresa -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Sistema de Lavanderia</h2>
                <p class="text-gray-600">Controle de Empacotamento e Entrega</p>
            </div>

            <!-- QR Code -->
            <div class="flex justify-center mb-8">
                <div class="p-4 border-2 border-gray-300 rounded-lg">
                    <!-- Aqui seria o QR Code real - por enquanto um placeholder -->
                    <div class="w-48 h-48 bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <p class="text-sm text-gray-500">QR Code</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Empacotamento -->
            <div class="space-y-4 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações do Empacotamento</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left max-w-2xl mx-auto">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código:</label>
                        <div class="text-lg font-bold text-primary-600">{{ $empacotamento->codigo_qr }}</div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coleta:</label>
                        <div class="text-gray-900">{{ $empacotamento->coleta->numero_coleta }}</div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estabelecimento:</label>
                        <div class="text-gray-900">{{ $empacotamento->coleta->estabelecimento->razao_social }}</div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Empacotamento:</label>
                        <div class="text-gray-900">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso Total:</label>
                        <div class="text-gray-900">{{ number_format($empacotamento->coleta->peso_total, 2, ',', '.') }} kg</div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total:</label>
                        <div class="text-gray-900">R$ {{ number_format($empacotamento->coleta->valor_total, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Instruções -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-semibold text-gray-900 mb-3">Instruções para Rastreamento</h4>
                <div class="text-sm text-gray-600 space-y-2">
                    <p>1. Escaneie o código QR com um leitor de QR Code</p>
                    <p>2. Ou acesse o sistema e digite o código: <strong>{{ $empacotamento->codigo_qr }}</strong></p>
                    <p>3. Acompanhe o status da entrega em tempo real</p>
                </div>
            </div>

            <!-- Rodapé -->
            <div class="border-t border-gray-200 pt-6 mt-8">
                <p class="text-xs text-gray-500">
                    Impresso em {{ now()->format('d/m/Y H:i') }} | Sistema de Lavanderia
                </p>
            </div>
        </div>
    </div>

    <!-- Resumo das Peças (não imprime) -->
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 print:hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Resumo das Peças</h3>
        </div>
        <div class="p-4">
            @if($empacotamento->coleta->pecas->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($empacotamento->coleta->pecas as $peca)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="font-medium text-gray-900">{{ $peca->tipo->nome }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $peca->quantidade }} peças | {{ number_format($peca->peso, 2, ',', '.') }} kg
                            </div>
                            <div class="text-sm font-medium text-primary-600">
                                R$ {{ number_format($peca->subtotal, 2, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center">Nenhuma peça encontrada.</p>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: white !important;
    }
    
    .print\:hidden {
        display: none !important;
    }
    
    .print\:shadow-none {
        box-shadow: none !important;
    }
    
    .print\:border-none {
        border: none !important;
    }
    
    /* Garantir que o QR Code seja impresso em tamanho adequado */
    .qr-code-container {
        page-break-inside: avoid;
    }
}
</style>
@endsection
