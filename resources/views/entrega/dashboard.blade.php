@extends('layouts.app')

@section('title', 'Gestão de Entregas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            Gestão de Entregas
        </h1>
        <p class="text-gray-600 mt-2">Gerencie suas entregas e confirme recebimentos</p>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Prontos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalProntos }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Em Trânsito</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEmTransito }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hoje</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEntreguesHoje }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEntreguesMotorista }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner QR Code -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
            </svg>
            Buscar Empacotamento
        </h2>
        
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" id="codigoEmpacotamento" 
                       placeholder="Digite ou escaneie o código QR do empacotamento"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button onclick="buscarEmpacotamento()" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Buscar
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="mostrarTab('prontos')" id="tab-prontos" 
                        class="tab-button active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Prontos para Entrega ({{ $totalProntos }})
                </button>
                <button onclick="mostrarTab('transito')" id="tab-transito" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Em Trânsito ({{ $totalEmTransito }})
                </button>
                <button onclick="mostrarTab('entregues')" id="tab-entregues" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Entregues Hoje ({{ $totalEntreguesHoje }})
                </button>
            </nav>
        </div>

        <!-- Tab Content: Prontos -->
        <div id="content-prontos" class="tab-content">
            <div class="p-6">
                @if($empacotamentosProntos->count() > 0)
                    <div class="space-y-4">
                        @foreach($empacotamentosProntos as $empacotamento)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="text-lg font-bold text-gray-900 mr-3">{{ $empacotamento->codigo_qr }}</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $empacotamento->coleta->numero_coleta }}
                                            </span>
                                        </div>
                                        <p class="text-gray-700 font-medium">{{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                                        <p class="text-sm text-gray-500">{{ $empacotamento->data_empacotamento->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <button onclick="confirmarSaida({{ $empacotamento->id }})" 
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        Confirmar Saída
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-gray-500">Nenhum empacotamento pronto para entrega</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Content: Em Trânsito -->
        <div id="content-transito" class="tab-content hidden">
            <div class="p-6">
                @if($empacotamentosEmTransito->count() > 0)
                    <div class="space-y-4">
                        @foreach($empacotamentosEmTransito as $empacotamento)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="text-lg font-bold text-gray-900 mr-3">{{ $empacotamento->codigo_qr }}</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Em Trânsito
                                            </span>
                                        </div>
                                        <p class="text-gray-700 font-medium">{{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                                        @if($empacotamento->entrega && $empacotamento->entrega->data_saida)
                                            <p class="text-sm text-gray-500">Saída: {{ $empacotamento->entrega->data_saida->format('d/m/Y H:i') }}</p>
                                        @endif
                                    </div>
                                    <button onclick="abrirModalEntrega({{ $empacotamento->id }})" 
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        Confirmar Entrega
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <p class="text-gray-500">Nenhuma entrega em trânsito</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Content: Entregues -->
        <div id="content-entregues" class="tab-content hidden">
            <div class="p-6">
                @if($empacotamentosEntregues->count() > 0)
                    <div class="space-y-4">
                        @foreach($empacotamentosEntregues as $empacotamento)
                            <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="text-lg font-bold text-gray-900 mr-3">{{ $empacotamento->codigo_qr }}</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Entregue
                                            </span>
                                        </div>
                                        <p class="text-gray-700 font-medium">{{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                                        @if($empacotamento->entrega && $empacotamento->entrega->data_entrega)
                                            <p class="text-sm text-gray-500">Entregue: {{ $empacotamento->entrega->data_entrega->format('d/m/Y H:i') }}</p>
                                        @endif
                                        @if($empacotamento->entrega && $empacotamento->entrega->motoristaEntrega)
                                            <p class="text-sm text-gray-500">Motorista: {{ $empacotamento->entrega->motoristaEntrega->nome }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">Nenhuma entrega realizada hoje</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Função para mostrar tabs
function mostrarTab(tabName) {
    // Esconder todos os conteúdos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remover classe active de todos os botões
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Mostrar conteúdo da tab selecionada
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Ativar botão da tab selecionada
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Função para buscar empacotamento
function buscarEmpacotamento() {
    const codigo = document.getElementById('codigoEmpacotamento').value.trim();

    if (!codigo) {
        alert('Digite o código do empacotamento');
        return;
    }

    fetch('{{ route("motorista.buscar-empacotamento") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ codigo: codigo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar informações do empacotamento
            alert(`Empacotamento encontrado!\nCódigo: ${data.empacotamento.codigo_qr}\nHotel: ${data.empacotamento.coleta.estabelecimento.razao_social}\nStatus: ${data.empacotamento.status.nome}`);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao buscar empacotamento');
    });
}

// Função para confirmar saída
function confirmarSaida(empacotamentoId) {
    if (!confirm('Confirmar que você vai entregar este empacotamento?')) {
        return;
    }

    fetch('{{ route("motorista.confirmar-saida") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ empacotamento_id: empacotamentoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao confirmar saída');
    });
}

// Função para abrir modal de entrega (será implementada)
function abrirModalEntrega(empacotamentoId) {
    alert('Modal de entrega será implementado em breve!\nEmpacotamento ID: ' + empacotamentoId);
}

// Event listener para Enter no campo de busca
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('codigoEmpacotamento').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarEmpacotamento();
        }
    });
});
</script>
@endpush
