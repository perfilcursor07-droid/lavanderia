@extends('layouts.app')

@section('title', 'Rastreamento de Peça Individual')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
            <svg class="inline w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Rastreamento de Peça Individual
        </h1>
        <p class="text-gray-600">Informações detalhadas da peça empacotada</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informações da Peça -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Informações da Peça
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Código QR da Peça</label>
                    <div class="text-lg font-mono font-bold text-blue-600">{{ $empacotamentoPeca->codigo_qr }}</div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Tipo de Peça</label>
                    <div class="text-lg font-semibold text-gray-900">{{ $empacotamentoPeca->tipo->nome }}</div>
                    <div class="text-sm text-gray-500">{{ $empacotamentoPeca->tipo->categoria }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Quantidade</label>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $empacotamentoPeca->quantidade }} peças
                        </div>
                    </div>
                    @if($empacotamentoPeca->peso > 0)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Peso</label>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ number_format($empacotamentoPeca->peso, 3, ',', '.') }} kg
                            </div>
                        </div>
                    @endif
                </div>

                @if($empacotamentoPeca->observacoes)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Observações</label>
                        <div class="text-gray-900 bg-gray-50 p-3 rounded-lg">
                            {{ $empacotamentoPeca->observacoes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informações do Empacotamento -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Empacotamento
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Código QR do Empacotamento</label>
                    <div class="text-lg font-mono font-bold text-green-600">
                        <a href="{{ route('qrcodes.rastrear', $empacotamentoPeca->empacotamento->codigo_qr) }}" 
                           class="hover:underline">
                            {{ $empacotamentoPeca->empacotamento->codigo_qr }}
                        </a>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Status Atual</label>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                              style="background-color: {{ $empacotamentoPeca->empacotamento->status->cor }}20; color: {{ $empacotamentoPeca->empacotamento->status->cor }};">
                            {{ $empacotamentoPeca->empacotamento->status->nome }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Estabelecimento</label>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $empacotamentoPeca->empacotamento->coleta->estabelecimento->razao_social }}
                    </div>
                    @if($empacotamentoPeca->empacotamento->coleta->estabelecimento->nome_fantasia)
                        <div class="text-sm text-gray-500">
                            {{ $empacotamentoPeca->empacotamento->coleta->estabelecimento->nome_fantasia }}
                        </div>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Número da Coleta</label>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $empacotamentoPeca->empacotamento->coleta->numero_coleta }}
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Data do Empacotamento</label>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $empacotamentoPeca->empacotamento->data_empacotamento->format('d/m/Y H:i') }}
                    </div>
                </div>

                @if($empacotamentoPeca->empacotamento->usuarioEmpacotamento)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Empacotado por</label>
                        <div class="text-gray-900">
                            {{ $empacotamentoPeca->empacotamento->usuarioEmpacotamento->nome }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="mt-8 text-center space-x-4">
        <a href="{{ route('empacotamento.show', $empacotamentoPeca->empacotamento->id) }}" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Ver Empacotamento Completo
        </a>

        <a href="{{ route('qrcodes.rastrear', $empacotamentoPeca->empacotamento->codigo_qr) }}" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Rastrear Empacotamento
        </a>
    </div>
</div>
@endsection

