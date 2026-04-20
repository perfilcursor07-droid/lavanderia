<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro - Sistema de Gestão de Lavanderia</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#e0e9ff',
                            200: '#c7d6fe',
                            300: '#a5b8fc',
                            400: '#8b93f8',
                            500: '#7c6df2',
                            600: '#6d4de6',
                            700: '#5d3dcb',
                            800: '#4c32a4',
                            900: '#402d82',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-500 via-purple-600 to-primary-700 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <!-- Logo e Título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Cadastro de Usuário</h1>
            <p class="text-white/80 text-lg">Sistema de Gestão de Lavanderia</p>
        </div>

        <!-- Card de Cadastro -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-red-800 font-medium">Erro no cadastro</h3>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cadastro.post') }}" class="space-y-6">
                @csrf

                <!-- Primeira linha: Nome e Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome Completo -->
                    <div class="space-y-2">
                        <label for="nome" class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Nome Completo
                        </label>
                        <input type="text"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('nome') border-red-500 @enderror"
                               id="nome"
                               name="nome"
                               value="{{ old('nome') }}"
                               placeholder="Seu nome completo"
                               required>
                        @error('nome')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                            Email
                        </label>
                        <input type="email"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-500 @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="seu@email.com"
                               required>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Segunda linha: Telefone e CPF -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Telefone -->
                    <div class="space-y-2">
                        <label for="telefone" class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Telefone
                        </label>
                        <input type="text"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('telefone') border-red-500 @enderror"
                               id="telefone"
                               name="telefone"
                               value="{{ old('telefone') }}"
                               placeholder="(11) 99999-9999"
                               maxlength="15">
                        @error('telefone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CPF -->
                    <div class="space-y-2">
                        <label for="cpf" class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                            CPF
                        </label>
                        <input type="text"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('cpf') border-red-500 @enderror"
                               id="cpf"
                               name="cpf"
                               value="{{ old('cpf') }}"
                               placeholder="000.000.000-00"
                               maxlength="14">
                        @error('cpf')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Nível de Acesso -->
                <div class="space-y-2">
                    <label for="nivel_acesso_id" class="flex items-center text-sm font-medium text-gray-700">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Nível de Acesso
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('nivel_acesso_id') border-red-500 @enderror"
                            id="nivel_acesso_id"
                            name="nivel_acesso_id"
                            required>
                        <option value="">Selecione um nível de acesso</option>
                        @foreach($niveisAcesso as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_acesso_id') == $nivel->id ? 'selected' : '' }}>
                                {{ $nivel->nome }} - {{ $nivel->descricao }}
                            </option>
                        @endforeach
                    </select>
                    @error('nivel_acesso_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terceira linha: Senhas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Senha -->
                    <div class="space-y-2">
                        <label for="password" class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Senha
                        </label>
                        <input type="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('password') border-red-500 @enderror"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="flex items-center text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Confirmar Senha
                        </label>
                        <input type="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="••••••••"
                               required>
                    </div>
                </div>

                <!-- Botão de Cadastro -->
                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Cadastrar
                    </span>
                </button>
            </form>

            <!-- Link para Login -->
            <div class="text-center mt-8">
                <p class="text-gray-600 mb-4">Já tem uma conta?</p>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-200 backdrop-blur-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Fazer Login
                </a>
            </div>
        </div>
    </div>

    <script>
        // Adicionar animações suaves
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('input, select');

            // Animação de entrada
            form.style.opacity = '0';
            form.style.transform = 'translateY(20px)';

            setTimeout(() => {
                form.style.transition = 'all 0.6s ease';
                form.style.opacity = '1';
                form.style.transform = 'translateY(0)';
            }, 100);

            // Efeito de foco nos inputs
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'transform 0.2s ease';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Máscara para CPF - Limita a 11 dígitos
            const cpfInput = document.getElementById('cpf');
            if (cpfInput) {
                cpfInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Remove não numéricos
                    
                    // Limita a 11 dígitos
                    value = value.substring(0, 11);
                    
                    // Aplica a máscara progressivamente
                    if (value.length <= 3) {
                        e.target.value = value;
                    } else if (value.length <= 6) {
                        e.target.value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
                    } else if (value.length <= 9) {
                        e.target.value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
                    } else {
                        e.target.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
                    }
                });
            }

            // Máscara para telefone - Limita a 11 dígitos
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Remove não numéricos
                    
                    // Limita a 11 dígitos
                    value = value.substring(0, 11);
                    
                    // Aplica a máscara progressivamente
                    if (value.length <= 2) {
                        e.target.value = value;
                    } else if (value.length <= 6) {
                        e.target.value = value.replace(/(\d{2})(\d{1,4})/, '($1) $2');
                    } else if (value.length <= 10) {
                        e.target.value = value.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
                    } else {
                        e.target.value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                    }
                });
            }
        });
    </script>
</body>
</html>
