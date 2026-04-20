@extends(auth()->user()->nivelAcesso && auth()->user()->nivelAcesso->nome === 'Motorista' ? 'layouts.motorista' : 'layouts.app')

@section('title', 'Nova Coleta RELAVE')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 flex items-center">
                <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                Nova Coleta RELAVE
            </h1>
            <p class="text-sm text-gray-600">Segunda lavagem de peças que retornaram - sem cobrança adicional</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('coletas.index-tipo', 'relave') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
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
    <form method="POST" action="{{ route('coletas.store-relave') }}" class="space-y-6">
        @csrf

        <!-- Informações da Coleta -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-6 h-6 bg-orange-100 text-orange-600 rounded-md flex items-center justify-center mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                Informações da Coleta RELAVE
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="estabelecimento_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Estabelecimento <span class="text-red-500">*</span>
                    </label>
                    <select name="estabelecimento_id" id="estabelecimento_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('estabelecimento_id') border-red-500 @enderror">
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
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Selecione o motorista</option>
                        @foreach($motoristas as $motorista)
                            <option value="{{ $motorista->id }}" {{ old('acompanhante_id') == $motorista->id ? 'selected' : '' }}>
                                {{ $motorista->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="data_agendamento" class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Agendamento <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="data_agendamento" id="data_agendamento" required
                           value="{{ old('data_agendamento', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('data_agendamento') border-red-500 @enderror">
                    @error('data_agendamento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="observacoes" id="observacoes" rows="3" 
                          placeholder="Observações sobre a coleta de relave..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('observacoes') }}</textarea>
            </div>
        </div>

        <!-- Peças Relave -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                Peças para RELAVE
            </h2>

            <div class="bg-orange-50 border border-orange-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-orange-800">
                            Sobre RELAVE
                        </h3>
                        <div class="mt-2 text-sm text-orange-700">
                            <p>• Segunda lavagem de peças que retornaram do cliente</p>
                            <p>• Não há cobrança adicional por ser relave</p>
                            <p>• Peças podem já ter etiqueta original ou precisar de nova</p>
                            <p>• Prazo normal de entrega</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="pecas-container">
                <div class="peca-item bg-gray-50 p-4 rounded-md mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de Peça <span class="text-red-500">*</span>
                            </label>
                            <select name="pecas[0][tipo_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div class="flex items-end">
                            <button type="button" onclick="removerPeca(this)" class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Remover
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código da Etiqueta Original</label>
                            <input type="text" name="pecas[0][codigo_etiqueta_original]" placeholder="Ex: EMP-123456-001" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <p class="text-xs text-gray-500 mt-1">Se a peça já possui etiqueta</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <input type="text" name="pecas[0][observacoes]" placeholder="Ex: Manchas persistentes..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="adicionarPeca()" class="w-full mt-4 px-4 py-2 border-2 border-dashed border-orange-300 text-orange-600 rounded-md hover:border-orange-400 hover:text-orange-700 transition-colors">
                + Adicionar Mais Peças Relave
            </button>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('coletas.index-tipo', 'relave') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                Criar Coleta RELAVE
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Peça <span class="text-red-500">*</span>
                    </label>
                    <select name="pecas[${pecaIndex}][tipo_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Selecione o tipo</option>
                        ${tipos.map(tipo => `<option value="${tipo.id}">${tipo.nome}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="pecas[${pecaIndex}][quantidade]" min="1" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="removerPeca(this)" class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Remover
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código da Etiqueta Original</label>
                    <input type="text" name="pecas[${pecaIndex}][codigo_etiqueta_original]" placeholder="Ex: EMP-123456-001" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Se a peça já possui etiqueta</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <input type="text" name="pecas[${pecaIndex}][observacoes]" placeholder="Ex: Manchas persistentes..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
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
</script>
@endpush

@endsection
