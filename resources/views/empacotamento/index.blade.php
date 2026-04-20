@extends('layouts.app')

@section('title', 'Empacotamentos')

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                📦 Empacotamentos
            </h1>
            <p class="text-sm text-gray-600">Gerencie o empacotamento e entrega das peças</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('empacotamento.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Empacotamento
            </a>
        </div>
    </div>

    <!-- Alerta para Empacotamentos com Lotes Pendentes -->
    @php
        $empacotamentosComPendentes = $empacotamentos->filter(function($emp) {
            return $emp->pecasIndividuais && $emp->pecasIndividuais->where('quantidade', 0)->count() > 0;
        });
        $totalComPendentes = $empacotamentosComPendentes->count();
    @endphp

    @if($totalComPendentes > 0 && !request('lotes_pendentes'))
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg" id="alerta-empacotamentos-pendentes">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-yellow-800">
                        Atenção: {{ $totalComPendentes }} empacotamento{{ $totalComPendentes > 1 ? 's' : '' }} com lotes pendentes
                    </h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        Você tem empacotamentos com lotes que não foram preenchidos. Estes lotes não receberão QR codes até serem processados.
                    </p>
                    <p class="mt-1 text-xs text-yellow-600">
                        💡 Dica: Use o filtro "Com lotes pendentes" ou clique no botão "Preencher" para completar os lotes
                    </p>
                    <div class="mt-3 flex space-x-3">
                        <a href="{{ route('empacotamento.index', ['lotes_pendentes' => '1']) }}"
                           class="inline-flex items-center px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtrar Pendentes
                        </a>
                        <button type="button" onclick="expandirTodosPendentes()"
                                class="inline-flex items-center px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Destacar na Lista
                        </button>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" onclick="fecharAlertaEmpacotamentos()" class="text-yellow-600 hover:text-yellow-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('empacotamento.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status_id" id="status_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <option value="">Todos os status</option>
                            @foreach($status as $st)
                                <option value="{{ $st->id }}" {{ request('status_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="motorista_id" class="block text-sm font-medium text-gray-700 mb-2">Motorista</label>
                        <select name="motorista_id" id="motorista_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <option value="">Todos os motoristas</option>
                            @foreach($motoristas as $motorista)
                                <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>
                                    {{ $motorista->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lotes_pendentes" class="block text-sm font-medium text-gray-700 mb-2">Lotes</label>
                        <select name="lotes_pendentes" id="lotes_pendentes"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <option value="">Todos os lotes</option>
                            <option value="1" {{ request('lotes_pendentes') == '1' ? 'selected' : '' }}>
                                ⚠️ Com lotes pendentes
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                        <input type="date" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                    <div>
                        <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                        <input type="date" name="data_fim" id="data_fim" value="{{ request('data_fim') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="busca" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <input type="text" name="busca" id="busca" value="{{ request('busca') }}" 
                               placeholder="Código QR, número da coleta ou estabelecimento..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('empacotamento.index') }}" 
                           class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Empacotamentos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Empacotamentos ({{ $empacotamentos->total() }})
                    </h3>
                    @if($totalComPendentes > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $totalComPendentes }} com lotes pendentes
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($empacotamentos->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código QR</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coleta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estabelecimento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peças</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motorista</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($empacotamentos as $empacotamento)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $empacotamento->codigo_qr }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($empacotamento->coleta)
                                        <div class="text-sm text-gray-900">{{ $empacotamento->coleta->numero_coleta }}</div>
                                    @else
                                        <div class="text-sm text-red-600">Coleta não encontrada</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($empacotamento->coleta && $empacotamento->coleta->estabelecimento)
                                        <div class="text-sm text-gray-900">{{ $empacotamento->coleta->estabelecimento->razao_social }}</div>
                                        @if($empacotamento->coleta->estabelecimento->nome_fantasia)
                                            <div class="text-xs text-gray-500">{{ $empacotamento->coleta->estabelecimento->nome_fantasia }}</div>
                                        @endif
                                    @else
                                        <div class="text-sm text-red-600">Estabelecimento não encontrado</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        @if($empacotamento->coleta && $empacotamento->coleta->pecas)
                                            @php
                                                $totalPecas = $empacotamento->coleta->pecas->sum(function($p) {
                                                    return $p->quantidade_empacotada > 0 ? $p->quantidade_empacotada : $p->quantidade;
                                                });
                                                $tiposPecas = $empacotamento->coleta->pecas->count();
                                                $pecasIndividuais = $empacotamento->pecasIndividuais ? $empacotamento->pecasIndividuais->count() : 0;
                                                $lotesPendentes = $empacotamento->pecasIndividuais ? $empacotamento->pecasIndividuais->where('quantidade', 0)->count() : 0;
                                                $lotesProcessados = $pecasIndividuais - $lotesPendentes;
                                            @endphp
                                            <div class="text-sm text-gray-900">
                                                <div class="font-medium">{{ $totalPecas }} peças</div>
                                                <div class="text-xs text-gray-500">{{ $tiposPecas }} tipos</div>
                                                @if($pecasIndividuais > 0)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $lotesProcessados }}/{{ $pecasIndividuais }} lotes
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                @if($pecasIndividuais > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $pecasIndividuais }} QR
                                                    </span>
                                                @endif
                                                @if($lotesPendentes > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $lotesPendentes }} pendente{{ $lotesPendentes > 1 ? 's' : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">-</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $empacotamento->status->cor }}20; color: {{ $empacotamento->status->cor }};">
                                        {{ $empacotamento->status->nome }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $empacotamento->motorista->nome ?? 'Não definido' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $empacotamento->data_empacotamento->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $empacotamento->data_empacotamento->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $lotesPendentes = $empacotamento->pecasIndividuais ? $empacotamento->pecasIndividuais->where('quantidade', 0)->count() : 0;
                                        @endphp

                                        @if($lotesPendentes > 0)
                                            <a href="{{ route('empacotamento.edit', $empacotamento->id) }}"
                                               class="inline-flex items-center px-2 py-1 bg-orange-100 hover:bg-orange-200 text-orange-700 text-xs font-medium rounded transition-colors duration-200"
                                               title="Preencher {{ $lotesPendentes }} lote{{ $lotesPendentes > 1 ? 's' : '' }} pendente{{ $lotesPendentes > 1 ? 's' : '' }}">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Preencher
                                            </a>
                                        @endif

                                        <a href="{{ route('empacotamento.show', $empacotamento->id) }}"
                                           class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors duration-200"
                                           title="Ver detalhes">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver
                                        </a>
                                        <a href="{{ route('qrcodes.rastrear', $empacotamento->codigo_qr) }}"
                                           class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded transition-colors duration-200"
                                           target="_blank"
                                           title="Abrir rastreamento">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            QR
                                        </a>
                                        @if($empacotamento->pecasIndividuais && $empacotamento->pecasIndividuais->count() > 0)
                                            <button onclick="mostrarQRCodesPecas({{ $empacotamento->id }})"
                                                    class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-medium rounded transition-colors duration-200"
                                                    title="Ver QR codes das peças">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h3a1 1 0 011 1v2h4a1 1 0 011 1v3a1 1 0 01-1 1h-2v9a1 1 0 01-1 1H8a1 1 0 01-1-1V9H5a1 1 0 01-1-1V5a1 1 0 011-1h2z"></path>
                                                </svg>
                                                Peças
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $empacotamentos->withQueryString()->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum empacotamento encontrado</h3>
                <p class="mt-1 text-sm text-gray-500">Comece criando um novo empacotamento.</p>
                <div class="mt-6">
                    <a href="{{ route('empacotamento.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Novo Empacotamento
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function mostrarQRCodesPecas(empacotamentoId) {
    // Redirecionar para a página de detalhes do empacotamento
    // onde os QR codes das peças são exibidos
    window.open(`{{ url('empacotamento') }}/${empacotamentoId}#qr-codes-pecas`, '_blank');
}

// Função para fechar alerta de empacotamentos pendentes
function fecharAlertaEmpacotamentos() {
    const alerta = document.getElementById('alerta-empacotamentos-pendentes');
    if (alerta) {
        alerta.style.display = 'none';
    }
}

// Função para destacar empacotamentos com lotes pendentes na lista
function expandirTodosPendentes() {
    // Encontrar todas as linhas com lotes pendentes
    const linhasComPendentes = [];
    document.querySelectorAll('tbody tr').forEach(linha => {
        const badgePendente = linha.querySelector('.bg-orange-100');
        if (badgePendente) {
            linhasComPendentes.push(linha);
        }
    });

    if (linhasComPendentes.length > 0) {
        // Remover destaque anterior
        document.querySelectorAll('tbody tr').forEach(linha => {
            linha.classList.remove('ring-4', 'ring-orange-400', 'ring-opacity-75', 'bg-orange-50');
        });

        // Destacar linhas com pendentes
        linhasComPendentes.forEach(linha => {
            linha.classList.add('ring-4', 'ring-orange-400', 'ring-opacity-75', 'bg-orange-50');
        });

        // Scroll para a primeira linha destacada
        if (linhasComPendentes[0]) {
            setTimeout(() => {
                linhasComPendentes[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 300);
        }

        // Mostrar mensagem temporária
        mostrarMensagemTemporaria(`${linhasComPendentes.length} empacotamento${linhasComPendentes.length > 1 ? 's' : ''} com lotes pendentes destacado${linhasComPendentes.length > 1 ? 's' : ''}`, 'warning');

        // Remover destaque após 5 segundos
        setTimeout(() => {
            linhasComPendentes.forEach(linha => {
                linha.classList.remove('ring-4', 'ring-orange-400', 'ring-opacity-75', 'bg-orange-50');
            });
        }, 5000);
    } else {
        mostrarMensagemTemporaria('Nenhum empacotamento com lotes pendentes encontrado nesta página', 'info');
    }
}

// Função para mostrar mensagem temporária
function mostrarMensagemTemporaria(mensagem, tipo = 'info') {
    const cores = {
        success: 'bg-green-100 border-green-200 text-green-800',
        warning: 'bg-yellow-100 border-yellow-200 text-yellow-800',
        error: 'bg-red-100 border-red-200 text-red-800',
        info: 'bg-blue-100 border-blue-200 text-blue-800'
    };

    const div = document.createElement('div');
    div.className = `fixed top-4 right-4 p-3 rounded border ${cores[tipo]} z-50 shadow-lg`;
    div.innerHTML = `
        <div class="flex items-center">
            <span class="text-sm font-medium">${mensagem}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-current opacity-70 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(div);

    // Remover após 3 segundos
    setTimeout(() => {
        if (div.parentElement) {
            div.remove();
        }
    }, 3000);
}

// Auto-aplicar filtro se vier da URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('lotes_pendentes') === '1') {
        // Destacar automaticamente os empacotamentos com lotes pendentes
        setTimeout(() => {
            expandirTodosPendentes();
        }, 500);
    }
});
</script>
@endpush
@endsection
