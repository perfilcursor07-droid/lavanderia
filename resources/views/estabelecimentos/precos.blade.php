@extends('layouts.app')

@section('title', 'Configurar Preços - ' . $estabelecimento->razao_social)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('estabelecimentos.show', $estabelecimento->id) }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configurar Preços</h1>
            <p class="text-gray-500 mt-1">{{ $estabelecimento->razao_social }}</p>
        </div>
    </div>

    <!-- Formulário -->
    <form action="{{ route('estabelecimentos.update-precos', $estabelecimento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <!-- Tipo de Precificação -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Tipo de Precificação *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ old('tipo_precificacao', $estabelecimento->tipo_precificacao) === 'peso' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" name="tipo_precificacao" value="peso" 
                            {{ old('tipo_precificacao', $estabelecimento->tipo_precificacao) === 'peso' ? 'checked' : '' }}
                            class="mt-1" onchange="togglePrecificacao()">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-900">Por Peso (Kg)</div>
                            <div class="text-sm text-gray-500">Preço único por quilograma</div>
                            <div class="text-xs text-gray-400 mt-1">Ex: R$ 1,75 por kg</div>
                        </div>
                    </label>

                    <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ old('tipo_precificacao', $estabelecimento->tipo_precificacao) === 'peca' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" name="tipo_precificacao" value="peca" 
                            {{ old('tipo_precificacao', $estabelecimento->tipo_precificacao) === 'peca' ? 'checked' : '' }}
                            class="mt-1" onchange="togglePrecificacao()">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-900">Por Peça</div>
                            <div class="text-sm text-gray-500">Preço único por peça</div>
                            <div class="text-xs text-gray-400 mt-1">Ex: R$ 5,00 por peça</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Preço por Kg -->
            <div id="preco-kg-section" class="{{ old('tipo_precificacao', $estabelecimento->tipo_precificacao) === 'peso' ? '' : 'hidden' }}">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <label for="preco_kg" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-5 h-5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                        </svg>
                        Preço por Kg (R$) *
                    </label>
                    <input type="number" name="preco_kg" id="preco_kg" step="0.01" min="0" 
                        value="{{ old('preco_kg', $estabelecimento->preco_kg) }}"
                        class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                        placeholder="0.00">
                    <p class="mt-2 text-sm text-gray-600">
                        <strong>Exemplo:</strong> Digite 1.75 para cobrar R$ 1,75 por quilograma
                    </p>
                </div>
            </div>

            <!-- Preço por Peça -->
            <div id="preco-peca-section" class="{{ old('tipo_precificacao', $estabelecimento->tipo_precificacao) === 'peca' ? '' : 'hidden' }}">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <label for="preco_peca" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-5 h-5 inline mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Preço por Peça (R$) *
                    </label>
                    <input type="number" name="preco_peca" id="preco_peca" step="0.01" min="0" 
                        value="{{ old('preco_peca', $estabelecimento->preco_peca) }}"
                        class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-lg font-semibold"
                        placeholder="0.00">
                    <p class="mt-2 text-sm text-gray-600">
                        <strong>Exemplo:</strong> Digite 5.00 para cobrar R$ 5,00 por peça (independente do tipo)
                    </p>
                </div>
            </div>

            <!-- Observações -->
            <div>
                <label for="observacoes_preco" class="block text-sm font-medium text-gray-700 mb-2">Observações sobre Preços</label>
                <textarea name="observacoes_preco" id="observacoes_preco" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Informações adicionais sobre a precificação...">{{ old('observacoes_preco', $estabelecimento->observacoes_preco) }}</textarea>
            </div>

            <!-- Informação sobre peças avulsas -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <strong>Importante:</strong> Peças avulsas continuam com preço individual conforme cadastrado na coleta.
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('estabelecimentos.show', $estabelecimento->id) }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Salvar Preços
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePrecificacao() {
    const tipoPrecificacao = document.querySelector('input[name="tipo_precificacao"]:checked').value;
    const precoKgSection = document.getElementById('preco-kg-section');
    const precoPecaSection = document.getElementById('preco-peca-section');

    if (tipoPrecificacao === 'peso') {
        precoKgSection.classList.remove('hidden');
        precoPecaSection.classList.add('hidden');
    } else {
        precoKgSection.classList.add('hidden');
        precoPecaSection.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
