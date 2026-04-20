<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistema de Gestão de Lavanderia</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    backdropBlur: {
                        xs: '2px',
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 flex items-center justify-center p-4 antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-brand-200/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-sm mx-auto">
        <!-- Logo e Título -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg shadow-brand-500/10 mb-6 group hover:shadow-xl transition-all duration-300">
                <svg class="w-10 h-10 text-brand-600 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-primary-900 mb-2">Bem-vindo</h1>
            <p class="text-primary-600 text-sm">Sistema de Gestão de Lavanderia</p>
        </div>

        <!-- Card de Login -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-brand-500/5 border border-white/20 p-8 transition-all duration-300 hover:shadow-2xl hover:shadow-brand-500/10">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200/50 rounded-xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-emerald-800 font-medium text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50/80 backdrop-blur-sm border border-red-200/50 rounded-xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-red-800 font-medium text-sm mb-1">Erro no login</h3>
                            <ul class="text-red-700 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="flex items-center">
                                        <span class="w-1 h-1 bg-red-400 rounded-full mr-2"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <!-- Campo CPF -->
                <div class="space-y-2">
                    <label for="cpf" class="block text-sm font-medium text-primary-700">
                        CPF
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-primary-400 group-focus-within:text-brand-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text"
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-primary-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all duration-200 placeholder-primary-400 @error('cpf') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                               id="cpf"
                               name="cpf"
                               value="{{ old('cpf') }}"
                               placeholder="000.000.000-00"
                               maxlength="14"
                               required
                               autofocus>
                    </div>
                    @error('cpf')
                        <p class="text-red-600 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Campo Senha -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-primary-700">
                        Senha
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-primary-400 group-focus-within:text-brand-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password"
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-primary-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all duration-200 placeholder-primary-400 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                               id="password"
                               name="password"
                               placeholder="••••••••••"
                               required>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Lembrar-me -->
                <div class="flex items-center pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox"
                               class="sr-only peer"
                               id="remember"
                               name="remember">
                        <div class="relative w-5 h-5 bg-white border-2 border-primary-300 rounded-md peer-checked:bg-brand-500 peer-checked:border-brand-500 transition-all duration-200 group-hover:border-brand-400">
                            <svg class="w-3 h-3 text-white absolute top-0.5 left-0.5 opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-sm text-primary-600 group-hover:text-primary-700 transition-colors duration-200">
                            Lembrar-me
                        </span>
                    </label>
                </div>

                <!-- Botão de Login -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 shadow-lg shadow-brand-500/25 hover:shadow-xl hover:shadow-brand-500/30 active:scale-[0.99]">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Entrar no Sistema
                        </span>
                    </button>
                </div>
            </form>


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos
            const loginCard = document.querySelector('.bg-white\\/80');
            const logoContainer = document.querySelector('.text-center.mb-10');
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('input[name="cpf"], input[type="password"]');
            const submitButton = document.querySelector('button[type="submit"]');
            
            // Animação inicial de entrada
            const elementsToAnimate = [logoContainer, loginCard];
            elementsToAnimate.forEach((element, index) => {
                if (element) {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(30px)';
                    
                    setTimeout(() => {
                        element.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, 100 + (index * 100));
                }
            });

            // Efeitos nos inputs
            inputs.forEach(input => {
                const inputGroup = input.closest('.relative.group');
                const icon = inputGroup?.querySelector('svg');
                
                // Máscara para CPF
                if (input.name === 'cpf') {
                    input.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length <= 11) {
                            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                            value = value.replace(/(\d{3})(\d{3})(\d{3})$/, '$1.$2.$3');
                            value = value.replace(/(\d{3})(\d{3})$/, '$1.$2');
                            value = value.replace(/(\d{3})$/, '$1');
                        }
                        e.target.value = value;
                    });
                }
                
                // Efeito de foco
                input.addEventListener('focus', function() {
                    if (inputGroup) {
                        inputGroup.style.transform = 'translateY(-2px)';
                        inputGroup.style.transition = 'transform 0.2s cubic-bezier(0.4, 0, 0.2, 1)';
                    }
                });

                input.addEventListener('blur', function() {
                    if (inputGroup) {
                        inputGroup.style.transform = 'translateY(0)';
                    }
                });

                // Animação de digitação
                input.addEventListener('input', function() {
                    if (icon) {
                        icon.style.transform = this.value ? 'scale(1.1)' : 'scale(1)';
                        icon.style.transition = 'transform 0.2s ease';
                    }
                });
            });

            // Efeito no botão de submit
            if (submitButton) {
                submitButton.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px) scale(1.01)';
                });

                submitButton.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });

                // Loading state no submit
                form.addEventListener('submit', function() {
                    const originalText = submitButton.innerHTML;
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <span class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Entrando...
                        </span>
                    `;
                    
                    // Restaurar estado se houver erro
                    setTimeout(() => {
                        if (document.querySelector('.bg-red-50\\/80')) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        }
                    }, 2000);
                });
            }

            // Animação de flutuação sutil no logo
            const logo = document.querySelector('.w-20.h-20');
            if (logo) {
                setInterval(() => {
                    logo.style.transform = 'translateY(-2px)';
                    setTimeout(() => {
                        logo.style.transform = 'translateY(0)';
                    }, 1000);
                }, 3000);
            }

            // Efeito de partículas no fundo (opcional)
            function createFloatingElements() {
                const container = document.body;
                for (let i = 0; i < 3; i++) {
                    const element = document.createElement('div');
                    element.className = 'fixed w-4 h-4 bg-brand-200/10 rounded-full pointer-events-none';
                    element.style.left = Math.random() * 100 + '%';
                    element.style.top = Math.random() * 100 + '%';
                    element.style.animation = `float ${5 + Math.random() * 5}s ease-in-out infinite`;
                    container.appendChild(element);

                    setTimeout(() => {
                        element.remove();
                    }, 10000);
                }
            }

            // CSS animation keyframes
            const style = document.createElement('style');
            style.textContent = `
                @keyframes float {
                    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
                    50% { transform: translateY(-20px) rotate(180deg); opacity: 0.7; }
                }
                
                .group:focus-within svg {
                    color: rgb(37 99 235);
                }
            `;
            document.head.appendChild(style);

            // Iniciar efeito de partículas
            createFloatingElements();
            setInterval(createFloatingElements, 5000);
        });
    </script>
</body>
</html>
