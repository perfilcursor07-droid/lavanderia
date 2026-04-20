@extends('layouts.app')

@section('title', 'Nova Pesagem')

@section('content')
<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                </svg>
                ⚖️ Nova Pesagem
            </h1>
            <p class="text-sm text-gray-600">Registrar nova pesagem de peças</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('pesagem.create-comparacao') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h5.586a1 1 0 00.707-.293l5.414-5.414a1 1 0 00.293-.707V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Pesagem com Comparação
            </a>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dados da Pesagem
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('pesagem.store-geral') }}" id="formPesagem">
                        @csrf

                        <div class="mb-4">
                            <label for="coleta_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Coleta <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('coleta_id') border-red-500 @enderror"
                                    id="coleta_id" name="coleta_id" required>
                                <option value="">Selecione uma coleta</option>
                                @foreach($coletas as $coletaOption)
                                    <option value="{{ $coletaOption->id }}"
                                            {{ (old('coleta_id', $coleta?->id) == $coletaOption->id) ? 'selected' : '' }}
                                            data-estabelecimento="{{ $coletaOption->estabelecimento->razao_social }}">
                                        {{ $coletaOption->numero_coleta }} - {{ $coletaOption->estabelecimento->razao_social }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Apenas coletas concluídas ou em andamento que ainda não possuem pesagem são exibidas
                            </p>
                            @error('coleta_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lista de Pesagens -->
                        <div class="mt-4">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    Pesagens <span class="text-red-500">*</span>
                                </label>
                            </div>

                            <div id="pesagens-container" class="space-y-3">
                                <!-- Primeira pesagem (modelo) -->
                                <div class="pesagem-item border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="0">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700">Pesagem #<span class="pesagem-numero">1</span></h4>
                                        <button type="button" onclick="removerPesagem(this)" class="text-red-600 hover:text-red-800 hidden remove-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                Peso (kg) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" step="0.01" min="0.01" max="999.99"
                                                   class="peso-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   name="pesagens[0][peso]" 
                                                   placeholder="0,00" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                Quantidade <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" min="1" max="999"
                                                   class="quantidade-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   name="pesagens[0][quantidade]" 
                                                   value="1" required>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Observação (opcional)</label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               name="pesagens[0][observacao]" 
                                               placeholder="Ex: Peças molhadas, danificadas...">
                                    </div>
                                </div>
                            </div>

                            <!-- Botão Adicionar Pesagem (abaixo das pesagens) -->
                            <div class="mt-3">
                                <button type="button" onclick="adicionarPesagem()" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 border-2 border-dashed border-blue-400 hover:border-blue-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Adicionar Pesagem
                                </button>
                            </div>

                            <!-- Totalizador -->
                            <div class="mt-4 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-sm font-semibold text-blue-900">Total Geral</h4>
                                        <p class="text-xs text-blue-700">Soma de todas as pesagens</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-blue-900" id="peso-total-geral">0,00 kg</div>
                                        <div class="text-sm text-blue-700"><span id="quantidade-total-geral">0</span> peças</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos para compatibilidade -->
                        <input type="hidden" id="peso" name="peso" value="0">
                        <input type="hidden" id="quantidade" name="quantidade" value="0">

                        <!-- Informações da Coleta -->
                        <div id="info-coleta-pesagem" style="display: none;" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h5 class="text-sm font-medium text-blue-900 mb-2">Informações da Coleta</h5>
                            <div id="dados-coleta-pesagem" class="text-sm text-blue-800"></div>
                            <div id="diferenca-peso" class="mt-2 text-sm font-medium"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="data_pesagem" class="block text-sm font-medium text-gray-700 mb-2">
                                    Data/Hora da Pesagem <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('data_pesagem') border-red-500 @enderror"
                                       id="data_pesagem" name="data_pesagem"
                                       value="{{ old('data_pesagem', now()->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('data_pesagem')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="local_pesagem" class="block text-sm font-medium text-gray-700 mb-2">Local da Pesagem</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('local_pesagem') border-red-500 @enderror"
                                       id="local_pesagem" name="local_pesagem"
                                       value="{{ old('local_pesagem') }}"
                                       placeholder="Ex: Balança 1, Setor A">
                                @error('local_pesagem')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="observacoes_gerais" class="block text-sm font-medium text-gray-700 mb-2">Observações Gerais</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('observacoes_gerais') border-red-500 @enderror"
                                      id="observacoes_gerais" name="observacoes_gerais" rows="3"
                                      placeholder="Observações gerais sobre a pesagem...">{{ old('observacoes_gerais') }}</textarea>
                            @error('observacoes_gerais')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('pesagem.index') }}"
                               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" name="status" value="rascunho"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Salvar como Rascunho
                            </button>
                            <button type="submit" name="status" value="concluida"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Concluir Pesagem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Informações da Coleta Selecionada -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hidden" id="infoColeta">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informações da Coleta
                    </h3>
                </div>
                <div class="p-4" id="dadosColeta">
                    <!-- Dados serão carregados via JavaScript -->
                </div>
            </div>

            <!-- Dicas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Dicas
                    </h3>
                </div>
                <div class="p-4">
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Certifique-se de que a balança está calibrada</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Registre o peso imediatamente após a pesagem</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Anote observações sobre peças danificadas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Confira se o tipo de peça está correto</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Calculadora de Peso -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Calculadora
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Peso Total:</span>
                        <span id="calcPesoTotal" class="text-sm font-bold text-blue-600">0,00 kg</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Peso Unitário:</span>
                        <span id="calcPesoUnitario" class="text-sm font-bold text-indigo-600">0,00 kg</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Valor Estimado:</span>
                        <span id="calcValorEstimado" class="text-sm font-bold text-green-600">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pesagemIndex = 1;
let coletaAtual = null;

// Função para adicionar nova pesagem
function adicionarPesagem() {
    const container = document.getElementById('pesagens-container');
    const novaPesagem = document.createElement('div');
    novaPesagem.className = 'pesagem-item border border-gray-200 rounded-lg p-4 bg-gray-50';
    novaPesagem.dataset.index = pesagemIndex;
    
    novaPesagem.innerHTML = `
        <div class="flex justify-between items-start mb-3">
            <h4 class="text-sm font-semibold text-gray-700">Pesagem #<span class="pesagem-numero">${pesagemIndex + 1}</span></h4>
            <button type="button" onclick="removerPesagem(this)" class="text-red-600 hover:text-red-800 remove-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Peso (kg) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" min="0.01" max="999.99"
                       class="peso-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                       name="pesagens[${pesagemIndex}][peso]" 
                       placeholder="0,00" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Quantidade <span class="text-red-500">*</span>
                </label>
                <input type="number" min="1" max="999"
                       class="quantidade-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                       name="pesagens[${pesagemIndex}][quantidade]" 
                       value="1" required>
            </div>
        </div>
        <div class="mt-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">Observação (opcional)</label>
            <input type="text" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                   name="pesagens[${pesagemIndex}][observacao]" 
                   placeholder="Ex: Peças molhadas, danificadas...">
        </div>
    `;
    
    container.appendChild(novaPesagem);
    pesagemIndex++;
    
    // Adicionar event listeners nos novos inputs
    const pesoInputs = novaPesagem.querySelectorAll('.peso-input');
    const qtdInputs = novaPesagem.querySelectorAll('.quantidade-input');
    
    pesoInputs.forEach(input => input.addEventListener('input', calcularTotais));
    qtdInputs.forEach(input => input.addEventListener('input', calcularTotais));
    
    // Atualizar botões de remover
    atualizarBotoesRemover();
    calcularTotais();
}

// Função para remover pesagem
function removerPesagem(btn) {
    const pesagemItem = btn.closest('.pesagem-item');
    pesagemItem.remove();
    
    // Renumerar pesagens
    const pesagens = document.querySelectorAll('.pesagem-item');
    pesagens.forEach((item, index) => {
        const numero = item.querySelector('.pesagem-numero');
        if (numero) numero.textContent = index + 1;
    });
    
    atualizarBotoesRemover();
    calcularTotais();
}

// Função para atualizar visibilidade dos botões de remover
function atualizarBotoesRemover() {
    const pesagens = document.querySelectorAll('.pesagem-item');
    const botoes = document.querySelectorAll('.remove-btn');
    
    if (pesagens.length <= 1) {
        botoes.forEach(btn => btn.classList.add('hidden'));
    } else {
        botoes.forEach(btn => btn.classList.remove('hidden'));
    }
}

// Função para calcular totais
function calcularTotais() {
    let pesoTotal = 0;
    let quantidadeTotal = 0;
    
    const pesagens = document.querySelectorAll('.pesagem-item');
    pesagens.forEach(pesagem => {
        const pesoInput = pesagem.querySelector('.peso-input');
        const qtdInput = pesagem.querySelector('.quantidade-input');
        
        const peso = parseFloat(pesoInput.value) || 0;
        const qtd = parseInt(qtdInput.value) || 0;
        
        pesoTotal += peso;
        quantidadeTotal += qtd;
    });
    
    // Atualizar displays principais
    document.getElementById('peso-total-geral').textContent = pesoTotal.toFixed(2) + ' kg';
    document.getElementById('quantidade-total-geral').textContent = quantidadeTotal;
    
    // Atualizar calculadora lateral
    const calcPesoTotal = document.getElementById('calcPesoTotal');
    const calcPesoUnitario = document.getElementById('calcPesoUnitario');
    
    if (calcPesoTotal) {
        calcPesoTotal.textContent = pesoTotal.toFixed(2).replace('.', ',') + ' kg';
    }
    
    if (calcPesoUnitario && quantidadeTotal > 0) {
        const pesoUnitario = pesoTotal / quantidadeTotal;
        calcPesoUnitario.textContent = pesoUnitario.toFixed(2).replace('.', ',') + ' kg';
    } else if (calcPesoUnitario) {
        calcPesoUnitario.textContent = '0,00 kg';
    }
    
    // Atualizar campos ocultos
    document.getElementById('peso').value = pesoTotal.toFixed(2);
    document.getElementById('quantidade').value = quantidadeTotal;
    
    // Calcular diferença com a coleta
    calcularDiferencaPesoTotal(pesoTotal);
    
    // Calcular valor estimado
    calcularValorEstimado(pesoTotal, quantidadeTotal);
}

// Função para calcular valor estimado
function calcularValorEstimado(pesoTotal, quantidadeTotal) {
    const valorDisplay = document.getElementById('calcValorEstimado');
    if (!valorDisplay || !coletaAtual || !coletaAtual.estabelecimento) {
        if (valorDisplay) valorDisplay.textContent = 'R$ 0,00';
        return;
    }

    const estabelecimento = coletaAtual.estabelecimento;
    let valorEstimado = 0;

    console.log('Estabelecimento:', estabelecimento);
    console.log('Tipo precificação:', estabelecimento.tipo_precificacao);
    console.log('Preço kg:', estabelecimento.preco_kg);
    console.log('Peso total:', pesoTotal);

    // Só calcula valor se for por peso (na pesagem)
    if (estabelecimento.tipo_precificacao === 'peso') {
        valorEstimado = pesoTotal * parseFloat(estabelecimento.preco_kg || 0);
        valorDisplay.textContent = 'R$ ' + valorEstimado.toFixed(2).replace('.', ',');
        valorDisplay.classList.remove('text-gray-400');
        valorDisplay.classList.add('text-green-600');
    } else {
        // Se for por peça, mostra mensagem
        valorDisplay.textContent = 'No empacotamento';
        valorDisplay.classList.remove('text-green-600');
        valorDisplay.classList.add('text-gray-400');
    }
}

// Função para calcular diferença de peso total
function calcularDiferencaPesoTotal(pesoInserido) {
    const diferencaPeso = document.getElementById('diferenca-peso');
    if (!coletaAtual || !diferencaPeso) return;

    const pesoColeta = coletaAtual.peso_total || 0;

    if (pesoColeta > 0) {
        const diferenca = pesoInserido - pesoColeta;
        let htmlDiferenca = '';

        if (Math.abs(diferenca) > 0.01) {
            const sinal = diferenca > 0 ? '+' : '';
            const cor = diferenca > 0 ? 'text-green-600' : 'text-red-600';
            const texto = diferenca > 0 ? 'a mais' : 'a menos';
            htmlDiferenca = `<div class="${cor}">Diferença: ${sinal}${Math.abs(diferenca).toFixed(2)} kg ${texto}</div>`;
        } else {
            htmlDiferenca = '<div class="text-green-600">✓ Peso confere com a coleta</div>';
        }

        diferencaPeso.innerHTML = htmlDiferenca;
    } else {
        if (pesoInserido > 0) {
            diferencaPeso.innerHTML = `<div class="text-blue-600">Peso da pesagem: ${pesoInserido.toFixed(2)} kg</div>`;
        } else {
            diferencaPeso.innerHTML = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const coletaSelect = document.getElementById('coleta_id');
    const infoColeta = document.getElementById('infoColeta');
    const dadosColeta = document.getElementById('dadosColeta');
    const infoColetaPesagem = document.getElementById('info-coleta-pesagem');
    const dadosColetaPesagem = document.getElementById('dados-coleta-pesagem');

    const baseUrl = '{{ url("coletas") }}';

    // Adicionar event listeners nos inputs iniciais
    const pesoInputs = document.querySelectorAll('.peso-input');
    const qtdInputs = document.querySelectorAll('.quantidade-input');
    
    pesoInputs.forEach(input => input.addEventListener('input', calcularTotais));
    qtdInputs.forEach(input => input.addEventListener('input', calcularTotais));

    // Função para carregar dados da coleta
    function carregarDadosColeta() {
        const coletaId = coletaSelect.value;
        if (!coletaId) {
            infoColeta.classList.add('hidden');
            infoColetaPesagem.style.display = 'none';
            coletaAtual = null;
            return;
        }

        fetch(`${baseUrl}/${coletaId}/pecas`)
            .then(response => response.json())
            .then(data => {
                coletaAtual = data.coleta;

                const coletaOption = coletaSelect.options[coletaSelect.selectedIndex];
                const estabelecimento = coletaOption.dataset.estabelecimento;

                dadosColeta.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Número:</span>
                            <div class="text-blue-600 font-semibold">${coletaOption.text.split(' - ')[0]}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Estabelecimento:</span>
                            <div class="text-gray-900 text-sm">${estabelecimento}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Status:</span>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Disponível para Pesagem
                                </span>
                            </div>
                        </div>
                    </div>
                `;

                let infoPesagem = '';
                if (data.coleta.peso_total > 0) {
                    infoPesagem = `<strong>Peso da Coleta:</strong> ${data.coleta.peso_total} kg`;
                } else {
                    infoPesagem = `<strong>Coleta por quantidade</strong> - Inserir peso da pesagem`;
                }

                if (data.pecas && data.pecas.length > 0) {
                    infoPesagem += `<br><strong>Tipos de peças:</strong> ${data.pecas.length}`;
                }

                dadosColetaPesagem.innerHTML = infoPesagem;

                infoColeta.classList.remove('hidden');
                infoColetaPesagem.style.display = 'block';

                calcularTotais();
            })
            .catch(error => {
                console.error('Erro ao carregar dados da coleta:', error);
                infoColetaPesagem.style.display = 'none';
            });
    }

    coletaSelect.addEventListener('change', carregarDadosColeta);

    if (coletaSelect.value) {
        carregarDadosColeta();
    }
    
    // Calcular totais iniciais
    calcularTotais();
});
</script>
@endpush
