@extends(auth()->user()->nivelAcesso && auth()->user()->nivelAcesso->nome === 'Motorista' ? 'layouts.motorista' : 'layouts.app')

@section('title', 'Nova Coleta DESENGOMA')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 flex items-center">
                <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                Nova Coleta DESENGOMA
            </h1>
            <p class="text-sm text-gray-600">Primeira lavagem de peças novas - prazo estendido de entrega</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('coletas.index-tipo', 'desengoma') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
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

    <!-- Formulário -->
    <form method="POST" action="{{ route('coletas.store-desengoma') }}" class="space-y-6">
        @csrf

        <!-- Informações da Coleta -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-6 h-6 bg-green-100 text-green-600 rounded-md flex items-center justify-center mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                Informações da Coleta DESENGOMA
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="estabelecimento_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Estabelecimento <span class="text-red-500">*</span>
                    </label>
                    <select name="estabelecimento_id" id="estabelecimento_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('estabelecimento_id') border-red-500 @enderror">
                        <option value="">Selecione o estabelecimento</option>
                        @foreach($estabelecimentos as $estabelecimento)
                            <option value="{{ $estabelecimento->id }}" {{ old('estabelecimento_id') == $estabelecimento->id ? 'selected' : '' }}>
                                {{ $estabelecimento->nome_fantasia }} - {{ $estabelecimento->razao_social }}
                            </option>
                        @endforeach
                    </select>
                    @error('estabelecimento_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="acompanhante_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Motorista/Acompanhante
                    </label>
                    <select name="acompanhante_id" id="acompanhante_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Selecione o motorista</option>
                        @foreach($motoristas as $motorista)
                            <option value="{{ $motorista->id }}" {{ old('acompanhante_id') == $motorista->id ? 'selected' : '' }}>
                                {{ $motorista->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="data_agendamento" class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Agendamento <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="data_agendamento" id="data_agendamento" required
                           value="{{ old('data_agendamento', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('data_agendamento') border-red-500 @enderror">
                    @error('data_agendamento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="data_prazo_entrega" class="block text-sm font-medium text-gray-700 mb-1">
                        Prazo de Entrega <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="data_prazo_entrega" id="data_prazo_entrega" required
                           value="{{ old('data_prazo_entrega', now()->addDays(7)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('data_prazo_entrega') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Prazo estendido para primeira lavagem (padrão: 7 dias)</p>
                    @error('data_prazo_entrega')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="observacoes" id="observacoes" rows="3" 
                          placeholder="Observações sobre a coleta de desengoma..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old('observacoes') }}</textarea>
            </div>
        </div>

        <!-- Peças para Desengoma -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                Peças para DESENGOMA
            </h2>

            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            Sobre DESENGOMA
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>• Primeira lavagem de peças novas</p>
                            <p>• Prazo de entrega estendido (normalmente 7 dias)</p>
                            <p>• Processo especial para remoção de goma industrial</p>
                            <p>• Coleta separada das peças regulares</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="pecas-container">
                <div class="peca-item bg-gray-50 p-4 rounded-md mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de Peça <span class="text-red-500">*</span>
                            </label>
                            <select name="pecas[0][tipo_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Selecione o tipo</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Quantidade <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="pecas[0][quantidade]" min="1" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                            <input type="number" name="pecas[0][peso]" min="0" step="0.01" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div class="flex items-end">
                            <button type="button" onclick="removerPeca(this)" class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Remover
                            </button>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <input type="text" name="pecas[0][observacoes]" placeholder="Ex: Roupas de cama novas do hotel..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <button type="button" onclick="adicionarPeca()" class="w-full mt-4 px-4 py-2 border-2 border-dashed border-green-300 text-green-600 rounded-md hover:border-green-400 hover:text-green-700 transition-colors">
                + Adicionar Mais Peças para Desengoma
            </button>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('coletas.index-tipo', 'desengoma') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                Criar Coleta DESENGOMA
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let pecaIndex = 1;

function adicionarPeca() {
    const container = document.getElementById('pecas-container');
    const tipos = @json($tipos->map(function($tipo) { return ['id' => $tipo->id, 'nome' => $tipo->nome]; }));
    
    const pecaHtml = `
        <div class="peca-item bg-gray-50 p-4 rounded-md mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Peça <span class="text-red-500">*</span>
                    </label>
                    <select name="pecas[${pecaIndex}][tipo_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Selecione o tipo</option>
                        ${tipos.map(tipo => `<option value="${tipo.id}">${tipo.nome}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="pecas[${pecaIndex}][quantidade]" min="1" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                    <input type="number" name="pecas[${pecaIndex}][peso]" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="removerPeca(this)" class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Remover
                    </button>
                </div>
            </div>

            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <input type="text" name="pecas[${pecaIndex}][observacoes]" placeholder="Ex: Roupas de cama novas do hotel..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', pecaHtml);
    pecaIndex++;
}

function removerPeca(button) {
    const pecaItem = button.closest('.peca-item');
    const container = document.getElementById('pecas-container');
    
    if (container.children.length > 1) {
        pecaItem.remove();
    } else {
        alert('Deve haver pelo menos uma peça na coleta.');
    }
}

// Definir data mínima para prazo de entrega
document.getElementById('data_agendamento').addEventListener('change', function() {
    const dataAgendamento = this.value;
    const dataPrazo = document.getElementById('data_prazo_entrega');
    
    if (dataAgendamento) {
        const date = new Date(dataAgendamento);
        date.setDate(date.getDate() + 1); // Mínimo 1 dia após agendamento
        dataPrazo.min = date.toISOString().split('T')[0];
        
        // Sugerir 7 dias após agendamento
        const suggestedDate = new Date(dataAgendamento);
        suggestedDate.setDate(suggestedDate.getDate() + 7);
        if (!dataPrazo.value) {
            dataPrazo.value = suggestedDate.toISOString().split('T')[0];
        }
    }
});
</script>
@endpush

@endsection
