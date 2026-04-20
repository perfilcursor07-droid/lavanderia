@extends('layouts.app')

@section('title', 'Detalhes da Pesagem')

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                </svg>
                ⚖️ Detalhes da Pesagem
            </h1>
            <p class="text-sm text-gray-600">Visualizar informações da pesagem</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            @if($pesagem->podeSerEditada())
                <a href="{{ route('pesagem.edit', $pesagem->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
            @endif
            <a href="{{ route('pesagem.index') }}"
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
            <!-- Dados Principais -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                        </svg>
                        Dados da Pesagem
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coleta:</label>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $pesagem->coleta->numero_coleta }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Peça:</label>
                            <div>
                                @if($pesagem->tipo)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $pesagem->tipo->nome }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Pesagem Geral
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Peso Total:</label>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $pesagem->peso_formatado }}
                            </div>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade:</label>
                            <div class="text-2xl font-bold text-indigo-600">
                                {{ $pesagem->quantidade }} peças
                            </div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Peso Unitário:</label>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $pesagem->peso_unitario_formatado }}
                            </div>
                        </div>
                    </div>

                    @if($pesagem->coleta && $pesagem->coleta->estabelecimento && $pesagem->coleta->estabelecimento->preco_kg > 0)
                    <div class="mb-6">
                        <div class="text-center p-4 bg-amber-50 border-2 border-amber-200 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">💰 Valor Calculado (Por Peso):</label>
                            <div class="text-3xl font-bold text-amber-600">
                                {{ $pesagem->valor_formatado }}
                            </div>
                            <div class="text-sm text-gray-600 mt-2">
                                {{ $pesagem->peso_formatado }} × R$ {{ number_format($pesagem->coleta->estabelecimento->preco_kg, 2, ',', '.') }}/kg
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data/Hora da Pesagem:</label>
                            <div class="text-gray-900">{{ $pesagem->data_pesagem_formatada }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Local da Pesagem:</label>
                            <div class="text-gray-900">{{ $pesagem->local_pesagem ?: 'Não informado' }}</div>
                        </div>
                    </div>

                    @if($pesagem->observacoes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observações:</label>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-gray-900">
                                {{ $pesagem->observacoes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações da Coleta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informações da Coleta
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estabelecimento:</label>
                            <div class="text-gray-900">{{ $pesagem->coleta->estabelecimento->razao_social }}</div>
                            @if($pesagem->coleta->estabelecimento->nome_fantasia)
                                <div class="text-sm text-gray-600">{{ $pesagem->coleta->estabelecimento->nome_fantasia }}</div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status da Coleta:</label>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color: {{ $pesagem->coleta->status->cor }}20; color: {{ $pesagem->coleta->status->cor }};">
                                    {{ $pesagem->coleta->status->nome }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Peso Total da Coleta:</label>
                            <div class="text-gray-900">{{ number_format($pesagem->coleta->peso_total, 2, ',', '.') }} kg</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valor Total da Coleta:</label>
                            <div class="text-gray-900">R$ {{ number_format($pesagem->coleta->valor_total, 2, ',', '.') }}</div>
                        </div>
                    </div>

                    @if($pesagem->coleta->observacoes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observações da Coleta:</label>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-gray-900">
                                {{ $pesagem->coleta->observacoes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Outras Peças da Coleta -->
            @if($pesagem->coleta->pecas->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Outras Peças da Coleta
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unitário</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pesagem->coleta->pecas as $peca)
                                    @php
                                        $estabelecimento = $pesagem->coleta->estabelecimento;
                                        if ($estabelecimento->tipo_precificacao === 'peso') {
                                            $precoUnitario = $estabelecimento->preco_kg;
                                            $subtotal = $peca->peso * $precoUnitario;
                                            $precoLabel = 'kg';
                                        } else {
                                            $precoUnitario = $estabelecimento->preco_peca;
                                            $subtotal = $peca->quantidade * $precoUnitario;
                                            $precoLabel = 'peça';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $peca->tipo ? $peca->tipo->nome : 'Tipo não definido' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $peca->quantidade }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($peca->peso, 2, ',', '.') }} kg</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($precoUnitario, 2, ',', '.') }}/{{ $precoLabel }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Ações da Pesagem -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Ações
                    </h3>
                </div>
                <div class="p-4">

                    <!-- Ações -->
                    <div class="space-y-2">
                        <a href="{{ route('pesagem.edit', $pesagem->id) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Pesagem
                        </a>

                        <form method="POST" action="{{ route('pesagem.destroy', $pesagem->id) }}"
                              onsubmit="return confirm('Tem certeza que deseja excluir esta pesagem?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Excluir Pesagem
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Responsável -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Responsável
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Pesagem realizada por:</span>
                        <div class="text-blue-600 font-medium">{{ $pesagem->usuario->nome }}</div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Data de Registro:</span>
                        <div class="text-gray-900 text-sm">{{ $pesagem->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Cálculos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Cálculos
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @if($pesagem->coleta && $pesagem->coleta->estabelecimento && $pesagem->coleta->estabelecimento->preco_kg > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Valor Calculado:</span>
                            <span class="text-lg font-bold text-green-600">
                                {{ $pesagem->valor_formatado }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Preço por kg:</span>
                            <span class="text-sm font-medium text-blue-600">R$ {{ number_format($pesagem->coleta->estabelecimento->preco_kg, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="text-xs text-gray-500 text-center">
                            {{ $pesagem->peso_formatado }} × R$ {{ number_format($pesagem->coleta->estabelecimento->preco_kg, 2, ',', '.') }}/kg
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-700">Tipo de Precificação:</span>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Por Peso (kg) - Pesagem
                                </span>
                            </div>
                        </div>
                    @elseif($pesagem->tipo)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Valor Estimado:</span>
                            <span class="text-lg font-bold text-green-600">
                                R$ {{ number_format($pesagem->peso * $pesagem->tipo->preco_kg, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Preço por kg:</span>
                            <span class="text-sm font-medium text-blue-600">R$ {{ number_format($pesagem->tipo->preco_kg, 2, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Categoria:</span>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $pesagem->tipo->categoria ?: 'Sem categoria' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span class="text-sm text-gray-500">
                                Pesagem geral - Informações de preço não disponíveis
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
