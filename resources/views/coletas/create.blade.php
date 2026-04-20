@extends(auth()->user()->nivelAcesso && auth()->user()->nivelAcesso->nome === 'Motorista' ? 'layouts.motorista' : 'layouts.app')

@section('title', 'Nova Coleta - Sistema de Gestão de Lavanderia')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
            <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nova Coleta
        </h1>
        <p class="text-sm text-gray-600">Agende uma nova coleta de roupas</p>
    </div>
    <div class="flex gap-2 mt-3 sm:mt-0">
        <a href="{{ route('coletas.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar
        </a>
    </div>
</div>

<!-- Formulário -->
<form method="POST" action="{{ route('coletas.store') }}" class="space-y-6">
    @csrf
    
    <!-- Informações Básicas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Informações da Coleta
        </h3>
        
        <!-- Tipo de Coleta -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Tipo de Coleta *
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Opção: Com Agendamento -->
                <label class="relative cursor-pointer">
                    <input type="radio" 
                           name="tipo_coleta" 
                           value="agendada" 
                           id="coleta_agendada"
                           class="sr-only peer"
                           {{ old('tipo_coleta', 'agendada') == 'agendada' ? 'checked' : '' }}
                           onchange="toggleAgendamento()">
                    <div class="bg-white border-2 border-gray-300 rounded-xl p-4 transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center peer-checked:bg-blue-500">
                                <svg class="w-5 h-5 text-blue-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1M8 7h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Com Agendamento</h4>
                                <p class="text-sm text-gray-600">Agendar para data e hora específicas</p>
                            </div>
                        </div>
                        <!-- Indicador de seleção -->
                        <div class="absolute top-3 right-3 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all duration-200">
                            <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity duration-200"></div>
                        </div>
                    </div>
                </label>

                <!-- Opção: Sem Agendamento -->
                <label class="relative cursor-pointer">
                    <input type="radio" 
                           name="tipo_coleta" 
                           value="imediata" 
                           id="coleta_imediata"
                           class="sr-only peer"
                           {{ old('tipo_coleta') == 'imediata' ? 'checked' : '' }}
                           onchange="toggleAgendamento()">
                    <div class="bg-white border-2 border-gray-300 rounded-xl p-4 transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center peer-checked:bg-green-500">
                                <svg class="w-5 h-5 text-green-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Sem Agendamento</h4>
                                <p class="text-sm text-gray-600">Coleta imediata, sem hora específica</p>
                            </div>
                        </div>
                        <!-- Indicador de seleção -->
                        <div class="absolute top-3 right-3 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-green-500 peer-checked:bg-green-500 transition-all duration-200">
                            <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity duration-200"></div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Estabelecimento -->
            <div>
                <label for="estabelecimento_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Estabelecimento *
                </label>
                <select id="estabelecimento_id" 
                        name="estabelecimento_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('estabelecimento_id') border-red-500 @enderror"
                        required>
                    <option value="">Selecione um estabelecimento</option>
                    @foreach($estabelecimentos as $estabelecimento)
                        <option value="{{ $estabelecimento->id }}" {{ old('estabelecimento_id') == $estabelecimento->id ? 'selected' : '' }}>
                            {{ $estabelecimento->razao_social }}
                            @if($estabelecimento->nome_fantasia)
                                ({{ $estabelecimento->nome_fantasia }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('estabelecimento_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Data de Agendamento (condicional) -->
            <div id="campo_agendamento">
                <label for="data_agendamento" class="block text-sm font-medium text-gray-700 mb-2">
                    Data e Hora do Agendamento *
                </label>
                <input type="datetime-local" 
                       id="data_agendamento" 
                       name="data_agendamento" 
                       value="{{ old('data_agendamento') }}"
                       min="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('data_agendamento') border-red-500 @enderror">
                @error('data_agendamento')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informação sobre coleta imediata (condicional) -->
            <div id="info_imediata" class="hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-green-800 mb-1">Coleta Imediata</h4>
                            <p class="text-sm text-green-700">
                                Esta coleta será registrada como disponível para execução imediata, 
                                sem necessidade de agendamento prévio.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="mt-6">
            <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">
                Observações
            </label>
            <textarea id="observacoes"
                      name="observacoes"
                      rows="3"
                      placeholder="Observações sobre a coleta..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('observacoes') border-red-500 @enderror">{{ old('observacoes') }}</textarea>
            @error('observacoes')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Motorista Acompanhante -->
        <div class="mt-6">
            <label for="acompanhante_id" class="block text-sm font-medium text-gray-700 mb-2">
                Motorista Acompanhante <span class="text-gray-500 text-xs">(opcional)</span>
            </label>
            <select id="acompanhante_id"
                    name="acompanhante_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('acompanhante_id') border-red-500 @enderror">
                <option value="">Selecione um motorista</option>
                @foreach($motoristas as $motorista)
                    <option value="{{ $motorista->id }}" {{ old('acompanhante_id') == $motorista->id ? 'selected' : '' }}>
                        {{ $motorista->nome }}
                    </option>
                @endforeach
            </select>
            @error('acompanhante_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Informações Adicionais -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Próximo Passo</h4>
                    <p class="text-sm text-blue-700">
                        Após criar a coleta, você será direcionado para adicionar as peças coletadas.
                        Isso permite um controle mais preciso do processo de coleta.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões -->
    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
        <a href="{{ route('coletas.index') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-xl transition-colors duration-200">
            Cancelar
        </a>
        <button type="submit" 
                id="submit_button"
                class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="submit_text">Agendar Coleta</span>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Função para alternar entre agendamento e coleta imediata
function toggleAgendamento() {
    const coletaAgendada = document.getElementById('coleta_agendada').checked;
    const coletaImediata = document.getElementById('coleta_imediata').checked;
    
    const campoAgendamento = document.getElementById('campo_agendamento');
    const infoImediata = document.getElementById('info_imediata');
    const dataAgendamento = document.getElementById('data_agendamento');
    const submitText = document.getElementById('submit_text');
    const submitButton = document.getElementById('submit_button');
    
    if (coletaAgendada) {
        // Mostrar campo de agendamento
        campoAgendamento.style.display = 'block';
        campoAgendamento.classList.remove('hidden');
        infoImediata.style.display = 'none';
        infoImediata.classList.add('hidden');
        
        // Tornar campo obrigatório
        dataAgendamento.required = true;
        dataAgendamento.removeAttribute('disabled');
        
        // Atualizar botão
        submitText.textContent = 'Agendar Coleta';
        submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        submitButton.classList.add('bg-primary-600', 'hover:bg-primary-700');
        
        // Animação de entrada
        campoAgendamento.style.opacity = '0';
        campoAgendamento.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            campoAgendamento.style.transition = 'all 0.3s ease';
            campoAgendamento.style.opacity = '1';
            campoAgendamento.style.transform = 'translateY(0)';
        }, 50);
        
    } else if (coletaImediata) {
        // Esconder campo de agendamento
        campoAgendamento.style.display = 'none';
        campoAgendamento.classList.add('hidden');
        infoImediata.style.display = 'block';
        infoImediata.classList.remove('hidden');
        
        // Remover obrigatoriedade
        dataAgendamento.required = false;
        dataAgendamento.value = '';
        dataAgendamento.setAttribute('disabled', 'disabled');
        
        // Atualizar botão
        submitText.textContent = 'Criar Coleta';
        submitButton.classList.remove('bg-primary-600', 'hover:bg-primary-700');
        submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
        
        // Animação de entrada
        infoImediata.style.opacity = '0';
        infoImediata.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            infoImediata.style.transition = 'all 0.3s ease';
            infoImediata.style.opacity = '1';
            infoImediata.style.transform = 'translateY(0)';
        }, 50);
    }
}

// Inicializar estado ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    toggleAgendamento();
    
    // Adicionar animações de entrada para os cards de opção
    const opcoes = document.querySelectorAll('label[class*="cursor-pointer"]');
    opcoes.forEach((opcao, index) => {
        opcao.style.opacity = '0';
        opcao.style.transform = 'translateY(20px)';
        setTimeout(() => {
            opcao.style.transition = 'all 0.4s ease';
            opcao.style.opacity = '1';
            opcao.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Adicionar efeitos de hover melhorados
    opcoes.forEach(opcao => {
        const div = opcao.querySelector('div[class*="border-2"]');
        
        opcao.addEventListener('mouseenter', () => {
            if (div) {
                div.style.transform = 'translateY(-2px)';
                div.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
            }
        });
        
        opcao.addEventListener('mouseleave', () => {
            if (div) {
                div.style.transform = 'translateY(0)';
                div.style.boxShadow = 'none';
            }
        });
    });
});

// Melhorar feedback visual ao clicar nas opções
document.querySelectorAll('input[name="tipo_coleta"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Adicionar uma animação de "bounce" ao selecionar
        const parentLabel = this.closest('label');
        const div = parentLabel.querySelector('div[class*="border-2"]');
        
        if (div) {
            div.style.transform = 'scale(0.98)';
            setTimeout(() => {
                div.style.transform = 'scale(1)';
            }, 100);
        }
    });
});
</script>

<style>
/* Melhorar as transições dos radio buttons customizados */
label[class*="cursor-pointer"] div[class*="border-2"] {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Animação para os indicadores de seleção */
.peer:checked ~ div div:last-child {
    animation: radioCheck 0.3s ease-in-out;
}

@keyframes radioCheck {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Melhorar as cores dos ícones quando selecionados */
.peer:checked ~ div .peer-checked\:bg-blue-500 svg,
.peer:checked ~ div .peer-checked\:bg-green-500 svg {
    color: white !important;
}

/* Estilo para campos desabilitados */
input:disabled {
    background-color: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

/* Transições suaves para mostrar/esconder elementos */
#campo_agendamento,
#info_imediata {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
</style>
@endpush
