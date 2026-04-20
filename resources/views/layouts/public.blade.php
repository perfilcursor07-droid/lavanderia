<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Acompanhe sua Coleta') - 212lavanderia</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #06b6d4 100%);
        }
        .hero-pattern {
            background-image: radial-gradient(circle at 25px 25px, rgba(255,255,255,0.1) 2px, transparent 0);
            background-size: 50px 50px;
        }
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header Público - Clean Design -->
    <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-1 12a2 2 0 002 2h6a2 2 0 002-2L16 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-gray-900">
                            212<span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">lavanderia</span>
                        </h1>
                        <p class="text-gray-600 text-[10px]">Acompanhamento de coletas</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="hidden md:flex items-center space-x-1.5 bg-green-50 rounded-full px-2.5 py-1 border border-green-100">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-medium text-green-700">Online</span>
                    </div>
                    <a href="{{ route('login') }}" 
                       class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-lg transition-all duration-300 font-medium shadow-md hover:shadow-lg text-xs transform hover:scale-105 active:scale-95">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Login</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer - Modern Design -->
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white mt-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                            <span class="text-lg">🧺</span>
                        </div>
                        <h3 class="text-xl font-bold">212lavanderia</h3>
                    </div>
                    <p class="text-gray-300 text-xs leading-relaxed max-w-md">
                        Sistema inteligente de acompanhamento de coletas. 
                        Transparência e qualidade em cada etapa do processo.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <div class="flex items-center space-x-2 bg-green-500/20 rounded-full px-2 py-1">
                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-xs font-medium text-green-300">Sistema Ativo</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-base font-semibold mb-4 text-gradient">Como Funciona</h3>
                    <ul class="text-gray-300 text-sm space-y-2">
                        <li class="flex items-center space-x-2">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                            <span>Digite seu CNPJ ou número da coleta</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full"></span>
                            <span>Acompanhe o status</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></span>
                            <span>Receba notificações de progresso</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-base font-semibold mb-4 text-gradient">Contato</h3>
                    <div class="text-gray-300 text-sm space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <span>(11) 99999-9999</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span>contato@212lavanderia.com</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span>São Paulo, SP</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700/50 mt-8 pt-8 text-center">
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} 212lavanderia. Todos os direitos reservados. • Sistema desenvolvido por erickdev.online
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @stack('scripts')
    
    <!-- Alert Messages -->
    @if(session('success'))
        <div id="alert-success" class="fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('alert-success').remove();
            }, 5000);
        </script>
    @endif

    @if(session('error'))
        <div id="alert-error" class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('alert-error').remove();
            }, 5000);
        </script>
    @endif
</body>
</html>