@extends(auth()->user()->nivelAcesso && auth()->user()->nivelAcesso->nome === 'Motorista' ? 'layouts.motorista' : 'layouts.app')

@section('title', 'Detalhes da Coleta - Sistema de Gestão de Lavanderia')

@section('content')
@if(!isset($coleta) || !$coleta)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Erro:</strong> Coleta não encontrada.
        <a href="{{ route('coletas.index') }}" class="underline">Voltar para listagem</a>
    </div>
@else

<!-- Alertas de Success/Warning/Error -->
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium">{{ session('warning') }}</p>
                @if(session('show_force_completion'))
                <div class="mt-3 flex gap-3">
                    <form method="POST" action="{{ route('coletas.concluir', $coleta->id) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="forcar_conclusao" value="1">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                onclick="return confirm('Tem certeza que deseja concluir esta coleta sem peças cadastradas?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Concluir Mesmo Assim
                        </button>
                    </form>
                    <a href="{{ route('coletas.add-pecas', $coleta->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Itens
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
@endif
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
            <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Coleta {{ $coleta->numero_coleta }}
        </h1>
        <p class="text-sm text-gray-600">Detalhes completos da coleta</p>
    </div>
    <div class="flex gap-2 mt-3 sm:mt-0">
        @if($coleta->podeSerCancelada() && $coleta->status->nome !== 'Concluída')
            @if($coleta->status->nome === 'Agendada')
                <form method="POST" action="{{ route('coletas.concluir', $coleta->id) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors duration-200"
                            onclick="return confirm('Confirma a conclusão desta coleta?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Concluir
                    </button>
                </form>
            @endif
            
            <button type="button" 
                    onclick="openCancelModal()"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancelar
            </button>
        @endif

        @if($coleta->podeReceberPesagens())
            <a href="{{ route('pesagem.create', ['coleta_id' => $coleta->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nova Pesagem
            </a>
        @endif

        <a href="{{ route('coletas.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar
        </a>
    </div>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @php
        $statusColors = [
            'Agendada' => 'bg-blue-100 text-blue-800',
            'Em andamento' => 'bg-yellow-100 text-yellow-800',
            'Concluída' => 'bg-green-100 text-green-800',
            'Cancelada' => 'bg-red-100 text-red-800',
        ];
        $colorClass = $statusColors[$coleta->status->nome] ?? 'bg-gray-100 text-gray-800';
    @endphp
    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $colorClass }}">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($coleta->status->nome === 'Concluída')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @elseif($coleta->status->nome === 'Cancelada')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @elseif($coleta->status->nome === 'Em andamento')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h1m5 0h8a2 2 0 002-2V9a2 2 0 00-2-2h-1"></path>
            @endif
        </svg>
        {{ $coleta->status->nome }}
    </span>
</div>

<!-- Card Principal com Informações -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <!-- Header do Card -->
    <div class="bg-gradient-to-r from-primary-50 to-primary-100 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $coleta->estabelecimento->razao_social }}</h2>
                @if($coleta->estabelecimento->nome_fantasia)
                <p class="text-sm text-gray-600 mt-1">{{ $coleta->estabelecimento->nome_fantasia }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-700">Número da Coleta</p>
                <p class="text-lg font-mono text-gray-900">{{ $coleta->numero_coleta }}</p>
            </div>
        </div>
    </div>

    <!-- Conteúdo do Card -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Coluna Esquerda - Informações da Coleta -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h1m5 0h8a2 2 0 002-2V9a2 2 0 00-2-2h-1"></path>
                    </svg>
                    Informações da Coleta
                </h3>
                
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Data Agendamento:</span>
                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($coleta->data_agendamento)->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    @if($coleta->data_coleta)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Data Coleta:</span>
                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($coleta->data_coleta)->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($coleta->data_conclusao)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Data Conclusão:</span>
                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($coleta->data_conclusao)->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Responsável:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $coleta->usuario->nome }}</span>
                    </div>

                    @if($coleta->acompanhante)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Acompanhante:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $coleta->acompanhante }}</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Cadastrado em:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $coleta->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                @if($coleta->observacoes)
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Observações:</h4>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-sm text-gray-700">{{ $coleta->observacoes }}</p>
                    </div>
                </div>
                @endif
                
                @if($coleta->motivo_cancelamento)
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Motivo do Cancelamento:</h4>
                    <div class="bg-red-50 rounded-lg p-3">
                        <p class="text-sm text-red-700">{{ $coleta->motivo_cancelamento }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Coluna Direita - Totais -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Resumo
                </h3>
                
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Peso Total</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($coleta->peso_total, 2, ',', '.') }} kg</p>
                        </div>
                        

                        
                        @php
                            $totalPecas = $coleta->pecas->sum('quantidade');
                        @endphp
                        @if($totalPecas > 0)
                        <div class="border-t border-green-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">Quantidade Total de Peças</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $totalPecas }} peças</p>
                        </div>
                        @endif

                        <div class="border-t border-green-200 pt-4">
                            <p class="text-sm text-gray-600 mb-1">Tipos de Peças</p>
                            <p class="text-lg font-medium text-gray-700">{{ $coleta->pecas->count() }} tipos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Peças da Coleta -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Peças da Coleta
        </h3>
        
        @if($coleta->pecas->count() == 0 && $coleta->status->nome == 'Agendada')
        <a href="{{ route('coletas.add-pecas', $coleta->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Adicionar Itens
        </a>
        @elseif($coleta->pecas->count() > 0 && $coleta->status->nome == 'Agendada')
        <a href="{{ route('coletas.add-pecas', $coleta->id) }}" 
           class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Editar Peças
        </a>
        @endif
    </div>
    
    @if($coleta->pecas->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd. Peças</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($coleta->pecas as $peca)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $peca->tipo ? $peca->tipo->nome : 'Tipo não definido' }}</div>
                            <div class="text-sm text-gray-500">{{ $peca->tipo ? $peca->tipo->categoria : 'Categoria não definida' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $peca->quantidade }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($peca->peso, 2, ',', '.') }} kg
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            @if($peca->quantidade > 0)
                                {{ $peca->quantidade }} {{ $peca->quantidade == 1 ? 'peça' : 'peças' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $peca->observacoes ?: '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <div class="relative">
            @if($coleta->status->nome == 'Agendada')
                <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            @endif
            <svg class="mx-auto h-12 w-12 {{ $coleta->status->nome == 'Agendada' ? 'text-yellow-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h3 class="mt-2 text-sm font-medium {{ $coleta->status->nome == 'Agendada' ? 'text-yellow-900' : 'text-gray-900' }}">
            Nenhuma peça cadastrada
        </h3>
        @if($coleta->status->nome == 'Agendada')
            <p class="mt-1 text-sm text-yellow-700 bg-yellow-50 rounded-lg p-3 mx-auto max-w-md">
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <strong>Atenção:</strong> Esta coleta não pode ser concluída sem peças cadastradas.
            </p>
            <div class="mt-6">
                <a href="{{ route('coletas.add-pecas', $coleta->id) }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-lg transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Adicionar Itens Agora
                </a>
            </div>
        @else
            <p class="mt-1 text-sm text-gray-500">Esta coleta foi processada sem peças cadastradas.</p>
        @endif
    </div>
    @endif
</div>

<!-- Pesagens da Coleta -->
@if($coleta->pesagens->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
            </svg>
            Pesagens Registradas
            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                {{ $coleta->pesagens->count() }}
            </span>
        </h3>
        <a href="{{ route('pesagem.index', ['coleta_id' => $coleta->id]) }}"
           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            Ver todas
        </a>
    </div>

    <!-- Resumo das Pesagens -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="text-sm text-blue-600 font-medium">Peso Total Pesado</div>
            <div class="text-xl font-bold text-blue-900">
                {{ number_format($coleta->pesoTotalPesagens(), 2, ',', '.') }} kg
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="text-sm text-gray-600 font-medium">Peso das Peças</div>
            <div class="text-xl font-bold text-gray-900">
                {{ number_format($coleta->peso_total, 2, ',', '.') }} kg
            </div>
        </div>
        <div class="bg-{{ $coleta->temDiferencaPeso() ? 'yellow' : 'green' }}-50 rounded-lg p-4">
            <div class="text-sm text-{{ $coleta->temDiferencaPeso() ? 'yellow' : 'green' }}-600 font-medium">
                Diferença
            </div>
            <div class="text-xl font-bold text-{{ $coleta->temDiferencaPeso() ? 'yellow' : 'green' }}-900">
                @if($coleta->diferencaPercentualPeso() !== null)
                    {{ number_format($coleta->diferencaPercentualPeso(), 1) }}%
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsável</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($coleta->pesagens->take(5) as $pesagem)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pesagem->data_pesagem->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $pesagem->tipo ? $pesagem->tipo->nome : 'Tipo não definido' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            {{ number_format($pesagem->peso, 2, ',', '.') }} kg
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pesagem->quantidade }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pesagem->usuario->nome }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pesagem->conferido)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Conferida
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('pesagem.show', $pesagem->id) }}"
                               class="text-blue-600 hover:text-blue-900">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($coleta->pesagens->count() > 5)
        <div class="mt-4 text-center">
            <a href="{{ route('pesagem.index', ['coleta_id' => $coleta->id]) }}"
               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Ver todas as {{ $coleta->pesagens->count() }} pesagens
            </a>
        </div>
    @endif
</div>
@endif

<!-- Modal de Cancelamento -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Cancelar Coleta</h3>
                <button type="button" onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('coletas.cancelar', $coleta->id) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="motivo_cancelamento" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo do Cancelamento *
                    </label>
                    <textarea id="motivo_cancelamento" 
                              name="motivo_cancelamento" 
                              rows="4"
                              placeholder="Descreva o motivo do cancelamento..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                              required></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeCancelModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Confirmar Cancelamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('motivo_cancelamento').value = '';
}

// Fechar modal ao clicar fora
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endpush
