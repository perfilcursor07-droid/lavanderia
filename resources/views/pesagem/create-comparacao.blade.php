@extends('layouts.app')

@section('title', 'Pesagem com Comparação')

@section('content')
<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                </svg>
                ⚖️ Pesagem com Comparação
            </h1>
            <p class="text-sm text-gray-600">Conferir e comparar valores das peças da coleta</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('pesagem.index') }}"
               class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Informações da Coleta -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Informações da Coleta
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Número da Coleta</p>
                    <p class="font-semibold text-gray-900">{{ $coleta->numero_coleta }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estabelecimento</p>
                    <p class="font-semibold text-gray-900">{{ $coleta->estabelecimento->razao_social }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Peso Total da Coleta</p>
                    <p class="font-semibold text-gray-900">{{ number_format($coleta->peso_total, 2, ',', '.') }} kg</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Pesagem -->
    <form method="POST" action="{{ route('pesagem.store-comparacao') }}" id="formPesagem">
        @csrf
        <input type="hidden" name="coleta_id" value="{{ $coleta->id }}">

        <!-- Dados Gerais da Pesagem -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dados Gerais da Pesagem
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>
        </div>

        <!-- Peças para Pesagem -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h5.586a1 1 0 00.707-.293l5.414-5.414a1 1 0 00.293-.707V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Peças da Coleta - Conferência e Pesagem
                </h3>
                <p class="text-sm text-gray-600 mt-1">Compare os valores da coleta com os valores da pesagem</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coleta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesagem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diferença</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($coleta->pecas as $peca)
                            <tr class="peca-row" data-peca-id="{{ $peca->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $peca->tipo->nome }}</div>
                                    <div class="text-sm text-gray-500">{{ $peca->tipo->categoria }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($peca->quantidade > 0)
                                            <strong>Quantidade:</strong> {{ $peca->quantidade }} peças
                                        @endif
                                        @if($peca->peso > 0)
                                            <strong>Peso:</strong> {{ number_format($peca->peso, 2, ',', '.') }} kg
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Peso (kg)</label>
                                            <input type="number" step="0.01" min="0"
                                                   name="pecas[{{ $peca->id }}][peso_pesagem]"
                                                   value="{{ old('pecas.'.$peca->id.'.peso_pesagem', $peca->peso) }}"
                                                   class="peso-pesagem w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   data-original="{{ $peca->peso }}"
                                                   required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade</label>
                                            <input type="number" min="0"
                                                   name="pecas[{{ $peca->id }}][quantidade_pesagem]"
                                                   value="{{ old('pecas.'.$peca->id.'.quantidade_pesagem', $peca->quantidade) }}"
                                                   class="quantidade-pesagem w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   data-original="{{ $peca->quantidade }}"
                                                   required>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="diferenca-display text-sm">
                                        <div class="diferenca-peso"></div>
                                        <div class="diferenca-quantidade"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Resumo das Diferenças -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6" id="resumo-diferencas" style="display: none;">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Resumo das Diferenças Encontradas
                </h3>
            </div>
            <div class="p-6">
                <div id="lista-diferencas"></div>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex justify-end space-x-3 mb-6">
            <a href="{{ route('pesagem.index') }}"
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Salvar Pesagem
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pesoInputs = document.querySelectorAll('.peso-pesagem');
    const quantidadeInputs = document.querySelectorAll('.quantidade-pesagem');

    // Função para calcular diferenças
    function calcularDiferencas() {
        let temDiferencas = false;
        const diferencas = [];

        document.querySelectorAll('.peca-row').forEach(function(row) {
            const pecaId = row.dataset.pecaId;
            const pesoInput = row.querySelector('.peso-pesagem');
            const quantidadeInput = row.querySelector('.quantidade-pesagem');
            const diferencaDisplay = row.querySelector('.diferenca-display');

            const pesoOriginal = parseFloat(pesoInput.dataset.original) || 0;
            const quantidadeOriginal = parseInt(quantidadeInput.dataset.original) || 0;
            const pesoPesagem = parseFloat(pesoInput.value) || 0;
            const quantidadePesagem = parseInt(quantidadeInput.value) || 0;

            const diferencaPeso = pesoPesagem - pesoOriginal;
            const diferencaQuantidade = quantidadePesagem - quantidadeOriginal;

            let htmlDiferenca = '';
            let temDiferencaNestaPeca = false;

            // Diferença de peso
            if (Math.abs(diferencaPeso) > 0.01) {
                const sinalPeso = diferencaPeso > 0 ? '+' : '';
                const corPeso = diferencaPeso > 0 ? 'text-green-600' : 'text-red-600';
                htmlDiferenca += `<div class="${corPeso} font-medium">Peso: ${sinalPeso}${diferencaPeso.toFixed(2)} kg</div>`;
                temDiferencaNestaPeca = true;
            }

            // Diferença de quantidade
            if (diferencaQuantidade !== 0) {
                const sinalQtd = diferencaQuantidade > 0 ? '+' : '';
                const corQtd = diferencaQuantidade > 0 ? 'text-green-600' : 'text-red-600';
                htmlDiferenca += `<div class="${corQtd} font-medium">Qtd: ${sinalQtd}${diferencaQuantidade}</div>`;
                temDiferencaNestaPeca = true;
            }

            if (!temDiferencaNestaPeca) {
                htmlDiferenca = '<div class="text-green-600 font-medium">✓ Confere</div>';
            } else {
                temDiferencas = true;
                const tipoNome = row.querySelector('.text-sm.font-medium.text-gray-900').textContent;
                diferencas.push({
                    tipo: tipoNome,
                    peso: diferencaPeso,
                    quantidade: diferencaQuantidade
                });
            }

            diferencaDisplay.innerHTML = htmlDiferenca;
        });

        // Mostrar/esconder resumo de diferenças
        const resumoDiv = document.getElementById('resumo-diferencas');
        const listaDiv = document.getElementById('lista-diferencas');

        if (temDiferencas) {
            resumoDiv.style.display = 'block';
            let htmlResumo = '<div class="space-y-2">';

            diferencas.forEach(function(diff) {
                htmlResumo += `<div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">`;
                htmlResumo += `<div class="font-medium text-gray-900">${diff.tipo}</div>`;

                if (Math.abs(diff.peso) > 0.01) {
                    const sinalPeso = diff.peso > 0 ? '+' : '';
                    const textoPeso = diff.peso > 0 ? 'a mais' : 'a menos';
                    htmlResumo += `<div class="text-sm text-gray-600">Peso: ${sinalPeso}${diff.peso.toFixed(2)} kg ${textoPeso}</div>`;
                }

                if (diff.quantidade !== 0) {
                    const sinalQtd = diff.quantidade > 0 ? '+' : '';
                    const textoQtd = diff.quantidade > 0 ? 'a mais' : 'a menos';
                    htmlResumo += `<div class="text-sm text-gray-600">Quantidade: ${sinalQtd}${diff.quantidade} peças ${textoQtd}</div>`;
                }

                htmlResumo += '</div>';
            });

            htmlResumo += '</div>';
            listaDiv.innerHTML = htmlResumo;
        } else {
            resumoDiv.style.display = 'none';
        }
    }

    // Adicionar event listeners
    pesoInputs.forEach(function(input) {
        input.addEventListener('input', calcularDiferencas);
    });

    quantidadeInputs.forEach(function(input) {
        input.addEventListener('input', calcularDiferencas);
    });

    // Calcular diferenças iniciais
    calcularDiferencas();
});
</script>
@endpush

@endsection
