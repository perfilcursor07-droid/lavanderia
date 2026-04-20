@extends('layouts.app')

@section('title', 'Editar Pesagem')

@section('content')
<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                </svg>
                ⚖️ Editar Pesagem
            </h1>
            <p class="text-sm text-gray-600">Editar dados da pesagem</p>
        </div>
        <div class="flex gap-2 mt-3 sm:mt-0">
            <a href="{{ route('pesagem.show', $pesagem->id) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Visualizar
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Dados da Pesagem
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('pesagem.update', $pesagem->id) }}" id="formPesagem">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="coleta_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Coleta <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('coleta_id') border-red-500 @enderror"
                                    id="coleta_id" name="coleta_id" required>
                                <option value="">Selecione uma coleta</option>
                                @foreach($coletas as $coletaOption)
                                    <option value="{{ $coletaOption->id }}"
                                            {{ (old('coleta_id', $pesagem->coleta_id) == $coletaOption->id) ? 'selected' : '' }}
                                            data-estabelecimento="{{ $coletaOption->estabelecimento->razao_social }}">
                                        {{ $coletaOption->numero_coleta }} - {{ $coletaOption->estabelecimento->razao_social }}
                                    </option>
                                @endforeach
                            </select>
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
                                <button type="button" onclick="adicionarPesagem()" 
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Adicionar Pesagem
                                </button>
                            </div>

                            <div id="pesagens-container" class="space-y-3">
                                <!-- Primeira pesagem (com dados existentes) -->
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
                                                   value="{{ old('pesagens.0.peso', $pesagem->peso) }}"
                                                   placeholder="0,00" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                Quantidade <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" min="1" max="999"
                                                   class="quantidade-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                   name="pesagens[0][quantidade]" 
                                                   value="{{ old('pesagens.0.quantidade', $pesagem->quantidade) }}" required>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Observação (opcional)</label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               name="pesagens[0][observacao]" 
                                               value="{{ old('pesagens.0.observacao') }}"
                                               placeholder="Ex: Peças molhadas, danificadas...">
                                    </div>
                                </div>
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
                        <input type="hidden" id="peso" name="peso" value="{{ old('peso', $pesagem->peso) }}">
                        <input type="hidden" id="quantidade" name="quantidade" value="{{ old('quantidade', $pesagem->quantidade) }}">

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
                                       value="{{ old('data_pesagem', $pesagem->data_pesagem->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('data_pesagem')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="local_pesagem" class="block text-sm font-medium text-gray-700 mb-2">Local da Pesagem</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('local_pesagem') border-red-500 @enderror"
                                       id="local_pesagem" name="local_pesagem"
                                       value="{{ old('local_pesagem', $pesagem->local_pesagem) }}"
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
                                      placeholder="Observações gerais sobre a pesagem...">{{ old('observacoes_gerais', $pesagem->observacoes) }}</textarea>
                            @error('observacoes_gerais')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status da Pesagem</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('status') border-red-500 @enderror"
                                    id="status" name="status">
                                <option value="rascunho" {{ old('status', $pesagem->status) == 'rascunho' ? 'selected' : '' }}>
                                    📝 Rascunho - Pode ser editada
                                </option>
                                <option value="concluida" {{ old('status', $pesagem->status) == 'concluida' ? 'selected' : '' }}>
                                    ✅ Concluída - Pesagem finalizada
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('pesagem.show', $pesagem->id) }}"
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Visualizar
                            </a>
                            <a href="{{ route('pesagem.index') }}"
                               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Atualizar Pesagem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Status da Pesagem -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status da Pesagem
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Status da Pesagem:</span>
                        <div class="mt-1">
                            @if($pesagem->isConcluida())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Concluída
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Rascunho
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-sm font-medium text-gray-700">Conferência:</span>
                        <div class="mt-1">
                            @if($pesagem->conferido)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Conferida
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pendente
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($pesagem->conferido)
                        <div>
                            <span class="text-sm font-medium text-gray-700">Conferida por:</span>
                            <div class="text-gray-900 text-sm">{{ $pesagem->usuarioConferencia->nome }}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Data da Conferência:</span>
                            <div class="text-gray-900 text-sm">{{ $pesagem->data_conferencia_formatada }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações da Coleta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Coleta Atual
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Número:</span>
                        <div class="text-blue-600 font-semibold">{{ $pesagem->coleta->numero_coleta }}</div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Estabelecimento:</span>
                        <div class="text-gray-900 text-sm">{{ $pesagem->coleta->estabelecimento->razao_social }}</div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color: {{ $pesagem->coleta->status->cor }}20; color: {{ $pesagem->coleta->status->cor }};">
                                {{ $pesagem->coleta->status->nome }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Histórico
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Criada por:</span>
                        <div class="text-gray-900 text-sm">{{ $pesagem->usuario->nome }}</div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Data de Criação:</span>
                        <div class="text-gray-900 text-sm">{{ $pesagem->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($pesagem->updated_at != $pesagem->created_at)
                        <div>
                            <span class="text-sm font-medium text-gray-700">Última Atualização:</span>
                            <div class="text-gray-900 text-sm">{{ $pesagem->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Calculadora de Peso -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Calculadora
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Peso Total:</span>
                        <span id="calcPesoTotal" class="text-sm font-bold text-blue-600">{{ $pesagem->peso_formatado }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Peso Unitário:</span>
                        <span id="calcPesoUnitario" class="text-sm font-bold text-indigo-600">{{ $pesagem->peso_unitario_formatado }}</span>
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
    
    // Atualizar displays
    document.getElementById('peso-total-geral').textContent = pesoTotal.toFixed(2) + ' kg';
    document.getElementById('quantidade-total-geral').textContent = quantidadeTotal;
    
    // Atualizar campos ocultos
    document.getElementById('peso').value = pesoTotal.toFixed(2);
    document.getElementById('quantidade').value = quantidadeTotal;
    
    // Calcular diferença com a coleta
    calcularDiferencaPesoTotal(pesoTotal);
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
            if (infoColeta) infoColeta.classList.add('hidden');
            infoColetaPesagem.style.display = 'none';
            coletaAtual = null;
            return;
        }

        fetch(`${baseUrl}/${coletaId}/pecas`)
            .then(response => response.json())
            .then(data => {
                coletaAtual = data.coleta;

                if (dadosColeta) {
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

                    if (infoColeta) infoColeta.classList.remove('hidden');
                }

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

                infoColetaPesagem.style.display = 'block';

                calcularTotais();
            })
            .catch(error => {
                console.error('Erro ao carregar dados da coleta:', error);
                infoColetaPesagem.style.display = 'none';
            });
    }

    coletaSelect.addEventListener('change', carregarDadosColeta);

    // Carregar dados iniciais
    carregarDadosColeta();
    
    // Calcular totais iniciais
    calcularTotais();
});
</script>
@endpush
