@extends('layouts.public')

@section('title', 'Rastreamento - ' . $codigo)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-white/20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Rastreamento</h1>
                        <div class="text-xs text-gray-600">Código: {{ $codigo }}</div>
                    </div>
                </div>
                <a href="{{ route('home') }}" 
                   class="text-gray-700 hover:bg-white px-3 py-2 rounded-lg transition-all duration-300 text-sm font-medium">
                    <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Início</span>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Informações do Pedido -->
        <div class="bg-white rounded-lg shadow-md border border-gray-100 mb-8 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-gray-50 to-blue-50/30 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Detalhes do Pedido</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="text-gray-600">Cliente:</span>
                                <span class="font-medium">{{ $empacotamento->coleta->estabelecimento->razao_social }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span class="text-gray-600">Empacotamento:</span>
                                <span class="font-medium">{{ $empacotamento->codigo_qr }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $empacotamento->status->cor }}20; color: {{ $empacotamento->status->cor }};">
                                    {{ $empacotamento->status->nome }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($empacotamento->coleta->pesagens->isNotEmpty())
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Informações da Pesagem</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($empacotamento->coleta->pesagens->sum('peso'), 2, ',', '.') }}</div>
                        <div class="text-sm text-blue-600">kg</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $empacotamento->coleta->pesagens->sum('quantidade') }}</div>
                        <div class="text-sm text-green-600">peças</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-md border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">Histórico do Pedido</h2>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @foreach($timeline as $index => $evento)
                    <div class="flex items-start space-x-4">
                        <!-- Ícone -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $evento['status'] === 'concluido' ? 'bg-green-100' : 
                                           ($evento['status'] === 'atual' ? 'bg-blue-100' : 'bg-gray-100') }}">
                                @switch($evento['icone'])
                                    @case('calendar')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        @break
                                    @case('truck')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        @break
                                    @case('scale')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        @break
                                    @case('package')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        @break
                                    @case('truck-delivery')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @break
                                    @case('check-circle')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        @break
                                    @case('user-check')
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'concluido' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5 {{ $evento['status'] === 'atual' ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                @endswitch
                            </div>
                        </div>

                        <!-- Conteúdo -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium {{ $evento['status'] === 'concluido' ? 'text-gray-900' : 
                                                                 ($evento['status'] === 'atual' ? 'text-blue-600' : 'text-gray-600') }}">
                                    {{ $evento['titulo'] }}
                                </h3>
                                @if($evento['data'])
                                <time class="text-sm text-gray-500">
                                    {{ $evento['data']->format('d/m/Y H:i') }}
                                </time>
                                @endif
                            </div>
                            <p class="text-gray-600 mt-1">{{ $evento['descricao'] }}</p>
                        </div>

                        <!-- Linha conectora -->
                        @if(!$loop->last)
                        <div class="absolute left-9 mt-12 w-0.5 h-6 bg-gray-200"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
