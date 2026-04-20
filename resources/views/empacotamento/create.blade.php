@extends('layouts.app')

@section('title', 'Novo Empacotamento')

@push('styles')
<style>
    .peca-extra-duplicada {
        background-color: #fefce8;
        border-left: 4px solid #eab308;
    }

    .peca-duplicada {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
    }

    .highlight-new {
        animation: highlightFade 2s ease-in-out;
    }

    @keyframes highlightFade {
        0% { background-color: #dbeafe; }
        100% { background-color: transparent; }
    }

    .btn-duplicate {
        transition: all 0.2s ease;
    }

    .btn-duplicate:hover {
        transform: scale(1.05);
    }

    .tipo-peca-container {
        transition: all 0.3s ease;
    }

    .tipo-peca-header:hover {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .tipo-peca-content {
        transition: all 0.3s ease;
    }

    .linha-empacotamento {
        transition: all 0.3s ease;
    }

    .linha-empacotamento:hover {
        background-color: #f8fafc;
        border-color: #e2e8f0;
    }

    .quantidade-linha:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                📦 Novo Empacotamento
            </h1>
            <p class="text-sm text-gray-600">Criar um novo empacotamento para entrega</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('empacotamento.index') }}" 
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Dados do Empacotamento
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('empacotamento.store') }}" id="formEmpacotamento">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="coleta_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Coleta <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('coleta_id') border-red-500 @enderror" 
                                        id="coleta_id" name="coleta_id" required>
                                    <option value="">Selecione uma coleta</option>
                                    @foreach($coletas as $coleta)
                                        <option value="{{ $coleta->id }}" 
                                                {{ old('coleta_id') == $coleta->id ? 'selected' : '' }}
                                                data-estabelecimento="{{ $coleta->estabelecimento->razao_social }}"
                                                data-peso="{{ $coleta->peso_total }}"
                                                data-valor="{{ $coleta->valor_total }}"
                                                data-pecas="{{ $coleta->pecas->count() }}">
                                            {{ $coleta->numero_coleta }} - {{ $coleta->estabelecimento->razao_social }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('coleta_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-4">
                            <label for="data_empacotamento" class="block text-sm font-medium text-gray-700 mb-2">
                                Data/Hora do Empacotamento <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('data_empacotamento') border-red-500 @enderror" 
                                   id="data_empacotamento" name="data_empacotamento" 
                                   value="{{ old('data_empacotamento', now()->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('data_empacotamento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Seção de Peças da Coleta - Nova Estrutura Hierárquica -->
                        <div id="secao-pecas-empacotamento" style="display: none;">
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h5.586a1 1 0 00.707-.293l5.414-5.414a1 1 0 00.293-.707V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span id="titulo-secao-pecas">Conferência de Quantidade de Peças</span>
                                </h4>
                                <p id="descricao-secao-pecas" class="text-sm text-gray-600 mb-4">Confira se a quantidade empacotada confere com a quantidade coletada</p>

                                <!-- Container dos Tipos de Peças -->
                                <div id="container-tipos-pecas" class="space-y-4">
                                    <!-- Os tipos de peças serão carregados via JavaScript -->
                                </div>

                                <!-- Botão Adicionar Peça Extra -->
                                <div class="mt-6 text-center">
                                    <button type="button" onclick="adicionarPecaExtra()"
                                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Adicionar Peça Extra
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="observacoes_empacotamento" class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('observacoes_empacotamento') border-red-500 @enderror"
                                      id="observacoes_empacotamento" name="observacoes_empacotamento" rows="3"
                                      placeholder="Observações sobre o empacotamento...">{{ old('observacoes_empacotamento') }}</textarea>
                            @error('observacoes_empacotamento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('empacotamento.index') }}" 
                               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Criar Empacotamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Informações da Coleta Selecionada -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" id="infoColeta" style="display: none;">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informações da Coleta
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Estabelecimento:</span>
                        <div id="estabelecimentoNome" class="text-gray-900 text-sm"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Peso Total:</span>
                        <div id="pesoTotal" class="text-gray-900 text-sm font-medium"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Valor Total:</span>
                        <div id="valorTotal" class="text-gray-900 text-sm font-medium"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Quantidade de Peças:</span>
                        <div id="quantidadePecas" class="text-gray-900 text-sm"></div>
                    </div>
                </div>
            </div>

            <!-- Dicas -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Fluxo do Empacotamento</h4>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li>• Apenas coletas concluídas podem ser empacotadas</li>
                            <li>• Um código QR será gerado automaticamente</li>
                            <li>• Status inicial: "Pronto para motorista"</li>
                            <li>• Motorista fará a saída lendo o QR Code</li>
                            <li>• Cliente assinará o recebimento na entrega</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const coletaSelect = document.getElementById('coleta_id');
    const infoColeta = document.getElementById('infoColeta');
    const secaoPecasEmpacotamento = document.getElementById('secao-pecas-empacotamento');
    // const tabelaPecasEmpacotamento = document.getElementById('tabela-pecas-empacotamento'); // Não usado no sistema hierárquico
    // const cabecalhoTabela = document.getElementById('cabecalho-tabela-empacotamento'); // Não usado no sistema hierárquico
    const tituloSecao = document.getElementById('titulo-secao-pecas');
    const descricaoSecao = document.getElementById('descricao-secao-pecas');
    // const botaoAdicionarPeca = document.getElementById('botao-adicionar-peca'); // Não usado no sistema hierárquico

    // URL base para as requisições
    const baseUrl = '{{ url("coletas") }}';

    // Tipos de peças disponíveis
    const tiposDisponiveis = @json($tipos ?? []);
    
    // Função para carregar dados da coleta e peças
    function carregarDadosColeta() {
        const coletaId = coletaSelect.value;
        if (!coletaId) {
            infoColeta.style.display = 'none';
            secaoPecasEmpacotamento.style.display = 'none';
            return;
        }

        // Fazer requisição AJAX para buscar as peças da coleta
        fetch(`${baseUrl}/${coletaId}/pecas`)
            .then(response => response.json())
            .then(data => {
                if (data.pecas && data.pecas.length > 0) {
                    carregarTabelaPecasEmpacotamento(data.pecas);
                    secaoPecasEmpacotamento.style.display = 'block';
                } else {
                    secaoPecasEmpacotamento.style.display = 'none';
                }

                // Atualizar informações da coleta
                const selectedOption = coletaSelect.options[coletaSelect.selectedIndex];
                const estabelecimento = selectedOption.dataset.estabelecimento;
                const peso = selectedOption.dataset.peso;
                const valor = selectedOption.dataset.valor;
                const pecas = selectedOption.dataset.pecas;

                document.getElementById('estabelecimentoNome').textContent = estabelecimento;
                document.getElementById('pesoTotal').textContent = peso + ' kg';
                document.getElementById('valorTotal').textContent = 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
                document.getElementById('quantidadePecas').textContent = data.pecas.length + ' tipos';

                infoColeta.style.display = 'block';
            })
            .catch(error => {
                console.error('Erro ao carregar peças:', error);
                secaoPecasEmpacotamento.style.display = 'none';
            });
    }

    // Event listener para mudança de coleta
    coletaSelect.addEventListener('change', carregarDadosColeta);

    // Função para carregar estrutura hierárquica de peças
    function carregarTabelaPecasEmpacotamento(pecas) {
        const containerTipos = document.getElementById('container-tipos-pecas');
        const pecasPorQuantidade = pecas.filter(peca => peca.quantidade > 0);

        if (pecasPorQuantidade.length > 0) {
            // Limpar container
            containerTipos.innerHTML = '';

            // Criar estrutura hierárquica para cada tipo de peça
            pecasPorQuantidade.forEach(function(peca, index) {
                const tipoContainer = criarContainerTipoPeca(peca, index);
                containerTipos.appendChild(tipoContainer);
            });
        } else {
            // Para coletas por peso total (sem tipos definidos)
            containerTipos.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Coleta por Peso Total</h4>
                            <p class="text-xs text-yellow-700 mt-1">Esta coleta foi feita por peso total. Defina os tipos e quantidades das peças empacotadas.</p>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Função para calcular diferenças de quantidade (para empacotamento)
    function calcularDiferencasQuantidade() {
        document.querySelectorAll('.peca-row').forEach(function(row) {
            const quantidadeInput = row.querySelector('.quantidade-empacotada');
            const diferencaDisplay = row.querySelector('.diferenca-display');

            if (!quantidadeInput || !diferencaDisplay) return;

            const quantidadeOriginal = parseInt(quantidadeInput.dataset.original) || 0;
            const quantidadeEmpacotada = parseInt(quantidadeInput.value) || 0;
            const diferencaQuantidade = quantidadeEmpacotada - quantidadeOriginal;

            let htmlDiferenca = '';

            if (diferencaQuantidade !== 0) {
                const sinalQtd = diferencaQuantidade > 0 ? '+' : '';
                const corQtd = diferencaQuantidade > 0 ? 'text-green-600' : 'text-red-600';
                const textoQtd = diferencaQuantidade > 0 ? 'a mais' : 'a menos';
                htmlDiferenca = `<div class="${corQtd} font-medium text-sm">${sinalQtd}${Math.abs(diferencaQuantidade)} peças ${textoQtd}</div>`;
            } else {
                htmlDiferenca = '<div class="text-green-600 font-medium text-sm">✓ Confere</div>';
            }

            diferencaDisplay.innerHTML = htmlDiferenca;
        });
    }

    // Função para criar HTML de linha de nova peça
    function criarLinhaNovaPeca(index) {
        let opcoesSelect = '<option value="">Selecione um tipo</option>';
        tiposDisponiveis.forEach(function(tipo) {
            opcoesSelect += `<option value="${tipo.id}">${tipo.nome} (${tipo.categoria})</option>`;
        });

        return `
            <tr class="linha-nova-peca">
                <td class="px-6 py-4 whitespace-nowrap">
                    <select name="novas_pecas[${index}][tipo_id]" class="tipo-select w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        ${opcoesSelect}
                    </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" min="1" name="novas_pecas[${index}][quantidade]"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="1" required>
                    <input type="hidden" name="novas_pecas[${index}][peso]" value="0">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button type="button" onclick="removerLinhaPeca(this)"
                            class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remover
                    </button>
                </td>
            </tr>
        `;
    }

    // Função para adicionar nova linha de peça (para coletas por peso)
    window.adicionarLinhaPeca = function() {
        const tabela = tabelaPecasEmpacotamento;
        const linhas = tabela.querySelectorAll('.linha-nova-peca');
        const novoIndex = linhas.length;

        // Criar opções do select
        let opcoesSelect = '<option value="">Selecione um tipo</option>';
        tiposDisponiveis.forEach(function(tipo) {
            opcoesSelect += `<option value="${tipo.id}">${tipo.nome} (${tipo.categoria})</option>`;
        });

        // Criar nova linha
        const novaLinha = document.createElement('tr');
        novaLinha.className = 'linha-nova-peca';
        novaLinha.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <select name="novas_pecas[${novoIndex}][tipo_id]" class="tipo-select w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    ${opcoesSelect}
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" min="1" name="novas_pecas[${novoIndex}][quantidade]"
                       class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="1" required>
                <input type="hidden" name="novas_pecas[${novoIndex}][peso]" value="0">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" onclick="removerLinhaPeca(this)"
                        class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Remover
                </button>
            </td>
        `;

        tabela.appendChild(novaLinha);
    };

    // Função para remover linha de peça
    window.removerLinhaPeca = function(botao) {
        const container = botao.closest('.tipo-peca-container');
        
        if (container) {
            // Para peças extras, pode remover sempre
            if (container.classList.contains('peca-extra-duplicada') || container.querySelector('.text-purple-600')) {
                container.remove();
            } else {
                // Para outros tipos, verificar se não é o único
                const containerTipos = document.getElementById('container-tipos-pecas');
                const totalContainers = containerTipos.querySelectorAll('.tipo-peca-container').length;
                
                if (totalContainers > 1) {
                    container.remove();
                } else {
                    alert('Deve haver pelo menos um tipo de peça.');
                }
            }
        }
    };

    // Função para duplicar peça existente (conferência de quantidade) - NÃO USADA NO SISTEMA HIERÁRQUICO
    /*
    window.duplicarPeca = function(pecaId) {
        const linhaOriginal = document.querySelector(`tr[data-peca-id="${pecaId}"]`);
        const tabela = tabelaPecasEmpacotamento;
        
        if (linhaOriginal) {
            // Obter dados da peça original
            const tipo = linhaOriginal.querySelector('td:first-child .text-sm.font-medium').textContent;
            const categoria = linhaOriginal.querySelector('td:first-child .text-sm.text-gray-500').textContent;
            const quantidadeOriginal = linhaOriginal.querySelector('.quantidade-empacotada').getAttribute('data-original');
            
            // Gerar um index único para a nova linha
            const novoIndex = Date.now();
            
            // Criar nova linha duplicada
            const novaLinha = document.createElement('tr');
            novaLinha.className = 'peca-row peca-duplicada';
            novaLinha.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${tipo}</div>
                    <div class="text-sm text-gray-500">${categoria}</div>
                    <div class="text-xs text-blue-600 font-medium">📋 Duplicada</div>
                    <input type="hidden" name="pecas_duplicadas[${novoIndex}][tipo_id]" value="${pecaId}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        <strong>${quantidadeOriginal}</strong> peças
                        <div class="text-xs text-gray-500">Quantidade original</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade Empacotada</label>
                        <input type="number" min="0"
                               name="pecas_duplicadas[${novoIndex}][quantidade]"
                               value="${quantidadeOriginal}"
                               class="quantidade-empacotada w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               data-original="${quantidadeOriginal}"
                               onchange="calcularDiferencasQuantidade()"
                               required>
                        <input type="hidden" name="pecas_duplicadas[${novoIndex}][peso]" value="0">
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="diferenca-display">
                        <div class="text-green-600 font-medium text-sm">✓ Confere</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex space-x-2">
                        <button type="button" onclick="duplicarPeca('${pecaId}')"
                                class="btn-duplicate inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors duration-200"
                                title="Duplicar novamente esta peça">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Duplicar
                        </button>
                        <button type="button" onclick="removerLinhaPeca(this)"
                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors duration-200"
                                title="Remover peça duplicada">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remover
                        </button>
                    </div>
                </td>
            `;
            
            // Inserir a nova linha após a linha original
            linhaOriginal.parentNode.insertBefore(novaLinha, linhaOriginal.nextSibling);
            
            // Scroll suave para a nova linha
            setTimeout(() => {
                novaLinha.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                
                // Adicionar animação de destaque
                novaLinha.classList.add('highlight-new');
                
                // Focar no campo de quantidade
                const inputQuantidade = novaLinha.querySelector('.quantidade-empacotada');
                if (inputQuantidade) {
                    inputQuantidade.focus();
                    inputQuantidade.select();
                }
            }, 100);
        }
    };
    */

    // Função para adicionar peça extra (conferência de quantidade)
    window.adicionarPecaExtra = function() {
        const containerTipos = document.getElementById('container-tipos-pecas');
        
        if (!containerTipos) {
            console.error('Container de tipos de peças não encontrado');
            alert('Erro: não foi possível encontrar o container de peças. Recarregue a página e tente novamente.');
            return;
        }

        const novoIndex = Date.now();
        
        // Criar opções do select
        let opcoesSelect = '<option value="">Selecione um tipo</option>';
        tiposDisponiveis.forEach(function(tipo) {
            opcoesSelect += `<option value="${tipo.id}">${tipo.nome} (${tipo.categoria})</option>`;
        });
        
        // Criar novo container de tipo de peça extra
        const novoTipoContainer = document.createElement('div');
        novoTipoContainer.className = 'tipo-peca-container bg-purple-50 border border-purple-200 rounded-lg';
        novoTipoContainer.innerHTML = `
            <div class="tipo-peca-header p-4 border-b border-purple-200 bg-purple-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <select name="pecas_extras[${novoIndex}][tipo_id]" 
                                    class="tipo-select bg-white border border-purple-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                                    required onchange="atualizarNomeTipoExtra(this, ${novoIndex})">
                                ${opcoesSelect}
                            </select>
                            <div class="text-xs text-purple-600 font-medium mt-1">✨ Peça Extra</div>
                        </div>
                    </div>
                    <button type="button" onclick="removerTipoExtra(this)"
                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                            title="Remover peça extra">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remover
                    </button>
                </div>
            </div>
            <div class="tipo-peca-content p-4">
                <div class="space-y-3">
                    <div class="linha-empacotamento bg-white border border-gray-200 rounded-lg p-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade Empacotada</label>
                                <input type="number" min="1"
                                       name="pecas_extras[${novoIndex}][quantidade]"
                                       value="1"
                                       class="quantidade-empacotada w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       required>
                                <input type="hidden" name="pecas_extras[${novoIndex}][peso]" value="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <div class="text-sm text-purple-600 font-medium pt-2">+ Extra</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Ações</label>
                                <button type="button" onclick="duplicarPecaExtra(this)"
                                        class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors"
                                        title="Duplicar esta peça extra">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Duplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        containerTipos.appendChild(novoTipoContainer);
        
        // Scroll para o novo item
        setTimeout(() => {
            novoTipoContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    };

    // Função para remover tipo de peça extra
    window.removerTipoExtra = function(botao) {
        const container = botao.closest('.tipo-peca-container');
        if (container) {
            container.remove();
        }
    };

    // Função para atualizar o nome do tipo de peça extra
    window.atualizarNomeTipoExtra = function(selectElement, index) {
        const nomeTipo = selectElement.options[selectElement.selectedIndex].text;
        const container = selectElement.closest('.tipo-peca-container');
        if (container) {
            container.querySelector('.tipo-peca-header .text-sm.font-medium').textContent = nomeTipo;
        }
    };

    // Função para duplicar peça extra
    window.duplicarPecaExtra = function(botao) {
        const containerOriginal = botao.closest('.tipo-peca-container');
        const containerTipos = document.getElementById('container-tipos-pecas');
        const novoIndex = Date.now();

        // Pegar dados do container original
        const tipoSelect = containerOriginal.querySelector('.tipo-select');
        const quantidadeOriginal = containerOriginal.querySelector('.quantidade-empacotada');

        // Criar opções do select
        let opcoesSelect = '<option value="">Selecione um tipo</option>';
        tiposDisponiveis.forEach(function(tipo) {
            opcoesSelect += `<option value="${tipo.id}"${tipo.id == tipoSelect.value ? ' selected' : ''}>${tipo.nome} (${tipo.categoria})</option>`;
        });

        // Criar nova linha duplicada
        const novoContainer = document.createElement('div');
        novoContainer.className = 'tipo-peca-container bg-purple-50 border border-purple-200 rounded-lg peca-extra-duplicada';
        novoContainer.innerHTML = `
            <div class="tipo-peca-header p-4 border-b border-purple-200 bg-purple-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <select name="pecas_extras[${novoIndex}][tipo_id]" 
                                    class="tipo-select bg-white border border-purple-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    required onchange="atualizarNomeTipoExtra(this, ${novoIndex})">
                                ${opcoesSelect}
                            </select>
                            <div class="text-xs text-blue-600 font-medium mt-1">📋 Extra Duplicada</div>
                        </div>
                    </div>
                    <button type="button" onclick="removerTipoExtra(this)"
                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                            title="Remover peça extra">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remover
                    </button>
                </div>
            </div>
            <div class="tipo-peca-content p-4">
                <div class="space-y-3">
                    <div class="linha-empacotamento bg-white border border-gray-200 rounded-lg p-3">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade Empacotada</label>
                                <input type="number" min="1"
                                       name="pecas_extras[${novoIndex}][quantidade]"
                                       value="${quantidadeOriginal.value}"
                                       class="quantidade-empacotada w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                <input type="hidden" name="pecas_extras[${novoIndex}][peso]" value="0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <div class="text-sm text-blue-600 font-medium pt-2">+ Duplicada</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Ações</label>
                                <button type="button" onclick="duplicarPecaExtra(this)"
                                        class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors"
                                        title="Duplicar esta peça extra">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Duplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Inserir depois do container original
        containerOriginal.insertAdjacentElement('afterend', novoContainer);

        // Adicionar classe de highlight
        novoContainer.classList.add('highlight-new');
        setTimeout(() => {
            novoContainer.classList.remove('highlight-new');
        }, 2000);

        // Scroll para o novo item
        setTimeout(() => {
            novoContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    };

    // Carregar dados iniciais se houver coleta selecionada
    if (coletaSelect.value) {
        carregarDadosColeta();
    }
});
</script>
@endpush
@endsection
