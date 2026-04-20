@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Relatórios</h1>
            <p class="text-gray-600">Análise detalhada de coletas, entregas e performance</p>
        </div>
        <button onclick="exportarRelatorio()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Exportar
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
        
        <form method="GET" action="{{ route('relatorios.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tipo de Relatório -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Relatório</label>
                <select name="tipo_relatorio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="coletas" {{ $tipoRelatorio == 'coletas' ? 'selected' : '' }}>Coletas</option>
                    <option value="entregas" {{ $tipoRelatorio == 'entregas' ? 'selected' : '' }}>Entregas</option>
                    <option value="financeiro" {{ $tipoRelatorio == 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                    <option value="performance" {{ $tipoRelatorio == 'performance' ? 'selected' : '' }}>Performance</option>
                </select>
            </div>

            <!-- Data Início -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Data Fim -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                <input type="date" name="data_fim" value="{{ $dataFim }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Estabelecimento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estabelecimento</label>
                <select name="estabelecimento_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    @foreach($estabelecimentos as $estabelecimento)
                        <option value="{{ $estabelecimento->id }}" {{ $estabelecimentoId == $estabelecimento->id ? 'selected' : '' }}>
                            {{ $estabelecimento->razao_social }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Motorista -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Motorista</label>
                <select name="motorista_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    @foreach($motoristas as $motorista)
                        <option value="{{ $motorista->id }}" {{ $motoristaId == $motorista->id ? 'selected' : '' }}>
                            {{ $motorista->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ $statusId == $status->id ? 'selected' : '' }}>
                            {{ $status->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botões -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </button>
                <a href="{{ route('relatorios.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    @if(isset($dados['estatisticas']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @if($dados['tipo'] == 'coletas')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total de Coletas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dados['estatisticas']['total_coletas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Peso Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($dados['estatisticas']['peso_total'], 2) }}kg</p>
                    </div>
                </div>
            </div>
        @endif

        @if($dados['tipo'] == 'entregas')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total de Entregas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dados['estatisticas']['total_entregas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Confirmadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dados['estatisticas']['entregas_confirmadas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tempo Médio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dados['estatisticas']['tempo_medio_entrega'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($dados['tipo'] == 'financeiro')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Receita Total</p>
                        <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($dados['estatisticas']['receita_total'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Receita Confirmada</p>
                        <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($dados['estatisticas']['receita_confirmada'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($dados['tipo'] == 'performance')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Taxa de Confirmação</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dados['estatisticas']['taxa_confirmacao'] }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tempo Médio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dados['estatisticas']['tempo_medio_entrega'] }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif

    <!-- Dados do Relatório -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Dados do Relatório - {{ ucfirst($dados['tipo']) }}
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            @if($dados['tipo'] == 'coletas' && isset($dados['coletas']))
                @include('relatorios.partials.coletas-table', ['coletas' => $dados['coletas']])
            @elseif($dados['tipo'] == 'entregas' && isset($dados['entregas']))
                @include('relatorios.partials.entregas-table', ['entregas' => $dados['entregas']])
            @elseif($dados['tipo'] == 'financeiro' && isset($dados['coletas']))
                @include('relatorios.partials.financeiro-table', ['coletas' => $dados['coletas']])
            @elseif($dados['tipo'] == 'performance' && isset($dados['entregas']))
                @include('relatorios.partials.performance-table', ['entregas' => $dados['entregas']])
            @endif
        </div>
    </div>
</div>

<script>
function exportarRelatorio() {
    // Implementar exportação
    alert('Funcionalidade de exportação em desenvolvimento');
}
</script>
@endsection
