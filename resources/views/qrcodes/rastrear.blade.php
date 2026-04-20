@extends('layouts.public')

@section('title', 'Rastreamento - ' . $empacotamento->codigo_qr)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Rastreamento de Empacotamento</h1>
            <p class="text-sm text-gray-600">Código: <span class="font-mono font-bold text-blue-600">{{ $empacotamento->codigo_qr }}</span></p>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">Status Atual</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color: {{ $empacotamento->status->cor ?? '#6b7280' }}20; color: {{ $empacotamento->status->cor ?? '#6b7280' }};">
                    <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $empacotamento->status->cor ?? '#6b7280' }};"></div>
                    {{ $empacotamento->status->nome }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Estabelecimento</p>
                    <p class="font-semibold text-gray-900">{{ $empacotamento->coleta->estabelecimento->razao_social ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Coleta</p>
                    <p class="font-semibold text-gray-900">{{ $empacotamento->coleta->numero_coleta ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Data Empacotamento</p>
                    <p class="font-semibold text-gray-900">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Peso Total</p>
                    <p class="font-semibold text-gray-900">{{ number_format($empacotamento->coleta->peso_total ?? 0, 2, ',', '.') }} kg</p>
                </div>
            </div>
        </div>

        <!-- Peças -->
        @if($empacotamento->pecasIndividuais && $empacotamento->pecasIndividuais->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4">
            <h2 class="text-base font-bold text-gray-900 mb-3">Sacolas ({{ $empacotamento->pecasIndividuais->count() }})</h2>
            <div class="space-y-2">
                @foreach($empacotamento->pecasIndividuais as $peca)
                    @php
                        $statusCor = match($peca->status_saida) {
                            'em_transito' => 'bg-yellow-100 text-yellow-800',
                            'entregue' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                        $statusTexto = match($peca->status_saida) {
                            'em_transito' => '🚚 Em trânsito',
                            'entregue' => '✅ Entregue',
                            'pronto' => '📦 Pronto',
                            default => '⏳ Aguardando',
                        };
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-sm text-gray-900">{{ $peca->tipo->nome ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $peca->quantidade }} peça(s) • {{ $peca->codigo_qr }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusCor }}">
                            {{ $statusTexto }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h2 class="text-base font-bold text-gray-900 mb-4">Histórico</h2>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Empacotado</p>
                        <p class="text-xs text-gray-500">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($empacotamento->entrega && $empacotamento->entrega->data_saida)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Saiu para entrega</p>
                        <p class="text-xs text-gray-500">{{ $empacotamento->entrega->data_saida->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @endif

                @if($empacotamento->entrega && $empacotamento->entrega->data_entrega)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Entregue</p>
                        <p class="text-xs text-gray-500">{{ $empacotamento->entrega->data_entrega->format('d/m/Y H:i') }}</p>
                        @if($empacotamento->entrega->nome_recebedor)
                            <p class="text-xs text-gray-500">Recebido por: {{ $empacotamento->entrega->nome_recebedor }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Voltar -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:text-blue-700">← Voltar ao início</a>
        </div>
    </div>
</div>
@endsection
