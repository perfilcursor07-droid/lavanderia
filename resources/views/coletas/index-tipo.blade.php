@extends(auth()->user()->nivelAcesso && auth()->user()->nivelAcesso->nome === 'Motorista' ? 'layouts.motorista' : 'layouts.app')

@section('title', 'Coletas ' . ucfirst($tipo))

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 flex items-center">
                @if($tipo === 'desengoma')
                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Coletas DESENGOMA
                @elseif($tipo === 'relave')
                    <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    Coletas RELAVE
                @else
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Coletas Normais
                @endif
            </h1>
            <p class="text-sm text-gray-600">
                @if($tipo === 'desengoma')
                    Primeira lavagem de peças novas - prazo estendido de entrega
                @elseif($tipo === 'relave')
                    Segunda lavagem de peças que retornaram - sem cobrança adicional
                @else
                    Coletas regulares do dia a dia
                @endif
            </p>
        </div>
        
        <div class="flex gap-2 mt-3 sm:mt-0">
            @if(auth()->user()->temPermissao('coletas.criar'))
                @if($tipo === 'desengoma')
                    <a href="{{ route('coletas.create-desengoma') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nova Desengoma
                    </a>
                @elseif($tipo === 'relave')
                    <a href="{{ route('coletas.create-relave') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nova Relave
                    </a>
                @else
                    <a href="{{ route('coletas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nova Coleta
                    </a>
                @endif
            @endif
            
            <a href="{{ route('coletas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Todas as Coletas
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Em Andamento</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['em_andamento'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Concluídas</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['concluidas'] }}</p>
                </div>
            </div>
        </div>

        @if($tipo === 'desengoma' && isset($stats['prazo_vencendo']))
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Prazo Vencendo</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['prazo_vencendo'] }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-gray-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">
                        @if($tipo === 'relave')
                            Sem Cobrança
                        @else
                            Este Mês
                        @endif
                    </p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if($tipo === 'relave')
                            100%
                        @else
                            --
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="estabelecimento_id" class="block text-sm font-medium text-gray-700 mb-1">Estabelecimento</label>
                <select name="estabelecimento_id" id="estabelecimento_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($estabelecimentos as $estabelecimento)
                        <option value="{{ $estabelecimento->id }}" {{ request('estabelecimento_id') == $estabelecimento->id ? 'selected' : '' }}>
                            {{ $estabelecimento->nome_fantasia }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status_id" id="status_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($status as $s)
                        <option value="{{ $s->id }}" {{ request('status_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                <input type="date" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                <input type="date" name="data_fim" id="data_fim" value="{{ request('data_fim') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('coletas.index-tipo', $tipo) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Limpar
                </a>
                @if($tipo === 'desengoma')
                    <label class="flex items-center ml-4">
                        <input type="checkbox" name="prazo_vencendo" value="1" {{ request('prazo_vencendo') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-gray-700">Apenas com prazo vencendo</span>
                    </label>
                @endif
            </div>
        </form>
    </div>

    <!-- Lista de Coletas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coleta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estabelecimento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        @if($tipo === 'desengoma')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prazo Entrega</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coletas as $coleta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg {{ $tipo === 'desengoma' ? 'bg-green-100 text-green-600' : ($tipo === 'relave' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }} flex items-center justify-center">
                                            @if($tipo === 'desengoma')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($tipo === 'relave')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $coleta->numero_coleta }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $coleta->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $coleta->estabelecimento->nome_fantasia }}</div>
                                <div class="text-sm text-gray-500">{{ $coleta->estabelecimento->razao_social }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $coleta->status->cor }}20; color: {{ $coleta->status->cor }};">
                                    {{ $coleta->status->nome }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $coleta->data_agendamento->format('d/m/Y H:i') }}
                            </td>
                            @if($tipo === 'desengoma')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($coleta->data_prazo_entrega)
                                        <span class="{{ $coleta->data_prazo_entrega <= now()->addDays(2) ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            {{ $coleta->data_prazo_entrega->format('d/m/Y') }}
                                        </span>
                                        @if($coleta->data_prazo_entrega <= now()->addDays(2))
                                            <div class="text-xs text-red-500">Vencendo!</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $tipo === 'desengoma' ? 'bg-green-100 text-green-800' : ($tipo === 'relave' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ strtoupper($tipo) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('coletas.show', $coleta->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                @if(auth()->user()->temPermissao('coletas.editar') && $coleta->podeSerEditada())
                                    <a href="#" class="text-green-600 hover:text-green-900">Editar</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tipo === 'desengoma' ? '7' : '6' }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Nenhuma coleta {{ $tipo }} encontrada</h3>
                                    <p class="text-gray-500 mb-4">
                                        @if($tipo === 'desengoma')
                                            Não há coletas de desengoma cadastradas com os filtros aplicados.
                                        @elseif($tipo === 'relave')
                                            Não há coletas de relave cadastradas com os filtros aplicados.
                                        @else
                                            Não há coletas normais cadastradas com os filtros aplicados.
                                        @endif
                                    </p>
                                    @if(auth()->user()->temPermissao('coletas.criar'))
                                        @if($tipo === 'desengoma')
                                            <a href="{{ route('coletas.create-desengoma') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Criar Primeira Coleta DESENGOMA
                                            </a>
                                        @elseif($tipo === 'relave')
                                            <a href="{{ route('coletas.create-relave') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Criar Primeira Coleta RELAVE
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($coletas->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $coletas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
