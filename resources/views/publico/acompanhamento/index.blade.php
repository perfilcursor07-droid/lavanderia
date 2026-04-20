@extends('layouts.public')

@section('title', 'Consulte sua Coleta')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 relative overflow-hidden">
    <!-- Background Animation Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-300/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-300/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/3 left-1/4 w-32 h-32 bg-green-300/20 rounded-full blur-2xl animate-bounce"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg mx-auto w-full">
            <!-- Logo and Title -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg animate-pulse">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-1 12a2 2 0 002 2h6a2 2 0 002-2L16 7"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    212<span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">lavanderia</span>
                </h1>
                <p class="text-gray-600 text-sm">Consulte o status da sua coleta</p>
            </div>

            <!-- Estabelecimento Info -->
            <div class="mb-5 bg-gradient-to-r from-blue-50/80 to-purple-50/80 backdrop-blur-lg rounded-xl p-3 border border-blue-200/30 animate-fade-in-delay">
                <div class="flex items-center space-x-2">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xs font-bold text-gray-900 mb-0.5 flex items-center">
                            <span class="mr-1">🏢</span> Para Estabelecimentos
                        </h3>
                        <p class="text-[11px] text-gray-700 leading-relaxed">
                            Digite seu <strong>CNPJ</strong> para ver todas as coletas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Search Form -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20 animate-slide-up">
                <form method="POST" action="{{ route('acompanhamento.buscar') }}" class="space-y-5">
                    @csrf

                    <!-- Messages -->
                    @if(session('error'))
                        <div class="bg-red-50/80 backdrop-blur-sm border border-red-200 text-red-700 px-4 py-3 rounded-2xl animate-shake">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-50/80 backdrop-blur-sm border border-green-200 text-green-700 px-4 py-3 rounded-2xl animate-bounce">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <!-- Input Field -->
                    <div class="space-y-2">
                        <label for="busca" class="block text-xs font-semibold text-gray-900">
                            <span class="flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m5 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-5 4h2.5m0 0L11 9m1.5 2L11 13"></path>
                                </svg>
                                CNPJ ou Número da Coleta
                            </span>
                        </label>
                        <div class="relative group">
                            <input type="text" 
                                   id="busca" 
                                   name="busca" 
                                   value="{{ old('busca') }}"
                                   class="w-full px-4 py-3 text-sm border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white group-hover:shadow-lg @error('busca') border-red-500 @enderror placeholder-gray-400"
                                   placeholder="Digite aqui para consultar..."
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <div class="w-7 h-7 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        @error('busca')
                            <div class="flex items-center space-x-2 text-red-600 animate-shake">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-medium">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105 active:scale-95">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Consultar Agora</span>
                        </span>
                    </button>
                </form>
                
                <!-- Quick Info -->
                <div class="mt-5 pt-5 border-t border-gray-200/50">
                    <p class="text-center text-[10px] text-gray-500 mb-2">Formatos aceitos:</p>
                    <div class="flex justify-center space-x-3">
                        <div class="flex items-center space-x-1.5 bg-gray-100/60 rounded-full px-2.5 py-1">
                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                            <span class="text-[10px] text-gray-700">CNPJ: 12.345.678/0001-90</span>
                        </div>
                        <div class="flex items-center space-x-1.5 bg-gray-100/60 rounded-full px-2.5 py-1">
                            <div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
                            <span class="text-[10px] text-gray-700">Coleta: COL-2024-001</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-3 gap-3 animate-fade-in-delay">
                <div class="bg-white/60 backdrop-blur-lg rounded-xl p-3 text-center border border-white/20 hover:bg-white/80 transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-900">Rápido</p>
                    <p class="text-[10px] text-gray-600">Consulta instantânea</p>
                </div>
                
                <div class="bg-white/60 backdrop-blur-lg rounded-xl p-3 text-center border border-white/20 hover:bg-white/80 transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-900">Seguro</p>
                    <p class="text-[10px] text-gray-600">Dados protegidos</p>
                </div>
                
                <div class="bg-white/60 backdrop-blur-lg rounded-xl p-3 text-center border border-white/20 hover:bg-white/80 transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-900">24/7</p>
                    <p class="text-[10px] text-gray-600">Sempre disponível</p>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-6 text-center animate-fade-in-delay">
                <p class="text-xs text-gray-500">
                    Precisa de ajuda? Entre em contato conosco
                </p>
                <div class="flex justify-center items-center space-x-4 mt-2">
                    <a href="tel:+5511999999999" class="flex items-center space-x-1 text-xs text-blue-600 hover:text-blue-700 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>(11) 99999-9999</span>
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="mailto:contato@212lavanderia.com.br" class="flex items-center space-x-1 text-xs text-blue-600 hover:text-blue-700 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>contato@212lavanderia.com.br</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .animate-fade-in {
        animation: fade-in 0.8s ease-out;
    }
    
    .animate-fade-in-delay {
        animation: fade-in 0.8s ease-out 0.3s both;
    }
    
    .animate-slide-up {
        animation: slide-up 0.6s ease-out 0.2s both;
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    /* Floating animation for background elements */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    
    /* Pulse glow effect */
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
    }
    
    .group:hover .bg-gradient-to-r {
        animation: pulse-glow 2s infinite;
    }
</style>

<script>
    // Enhanced auto format CNPJ with visual feedback
    document.getElementById('busca').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Se tem 11 dígitos ou mais, pode ser CNPJ
        if (value.length >= 11) {
            // Formatar como CNPJ: XX.XXX.XXX/XXXX-XX
            if (value.length <= 14) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*/, function(match, p1, p2, p3, p4, p5) {
                    let formatted = p1 + '.' + p2 + '.' + p3 + '/' + p4;
                    if (p5) formatted += '-' + p5;
                    return formatted;
                });
                e.target.value = value;
            }
        }
        
        // Add visual feedback
        if (value.length > 0) {
            e.target.classList.add('border-blue-400');
            e.target.classList.remove('border-gray-200');
        } else {
            e.target.classList.remove('border-blue-400');
            e.target.classList.add('border-gray-200');
        }
    });

    // Enhanced keypress validation
    document.getElementById('busca').addEventListener('keypress', function(e) {
        const allowedChars = /[0-9A-Za-z.\-\/]/;
        if (!allowedChars.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
            // Add shake animation for invalid input
            e.target.classList.add('animate-shake');
            setTimeout(() => {
                e.target.classList.remove('animate-shake');
            }, 500);
        }
    });
    
    // Focus and blur effects
    document.getElementById('busca').addEventListener('focus', function(e) {
        e.target.parentElement.classList.add('ring-4', 'ring-blue-500/20');
    });
    
    document.getElementById('busca').addEventListener('blur', function(e) {
        e.target.parentElement.classList.remove('ring-4', 'ring-blue-500/20');
    });
    
    // Form submission with loading state
    document.querySelector('form').addEventListener('submit', function(e) {
        const button = e.target.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        button.innerHTML = `
            <span class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Consultando...</span>
            </span>
        `;
        
        button.disabled = true;
        button.classList.add('opacity-75');
    });
</script>
@endpush
@endsection

