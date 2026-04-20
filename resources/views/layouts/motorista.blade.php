<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Entregas - Lavanderia')</title>

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
                    },
                    fontSize: {
                        'xs': ['0.65rem', { lineHeight: '1rem' }],
                        'sm': ['0.75rem', { lineHeight: '1.25rem' }],
                        'base': ['0.8rem', { lineHeight: '1.4rem' }],
                        'lg': ['0.9rem', { lineHeight: '1.5rem' }],
                        'xl': ['1rem', { lineHeight: '1.6rem' }],
                        '2xl': ['1.2rem', { lineHeight: '1.8rem' }],
                        '3xl': ['1.5rem', { lineHeight: '2rem' }],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans text-sm">
    <div class="flex min-h-screen">
        <!-- Mobile menu button -->
        <div class="lg:hidden fixed top-4 left-4 z-50">
            <button id="mobile-menu-button" class="p-3 bg-slate-900 text-white rounded-xl shadow-xl border border-slate-700 backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Sidebar -->
        <nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-60 bg-white border-r border-gray-100 shadow-sm transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="px-4 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-1 12a2 2 0 002 2h6a2 2 0 002-2L16 7"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-1.5 -right-1.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white shadow-sm"></div>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 leading-tight">Lavanderia</h2>
                            <p class="text-gray-500 text-[11px]">Gestão de Entregas</p>
                        </div>
                    </div>
                </div>

                <!-- Menu do Motorista -->
                <div class="flex-1 px-3 py-3 overflow-y-auto">
                    <div class="space-y-3">
                        @if(auth()->user()->temPermissao('coletas.visualizar'))
                        <!-- Coletas -->
                        <div class="mb-3" id="menu-coletas">
                            <h3 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Coletas</h3>
                            <a href="{{ route('coletas.index') }}" class="group flex items-center px-2.5 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('coletas.*') ? 'bg-green-50 text-green-700 border-r-2 border-green-600' : '' }}">
                                <div class="flex items-center justify-center w-7 h-7 rounded-lg {{ request()->routeIs('coletas.*') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200' }} transition-colors duração-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium">Coletas</p>
                                    <p class="text-xs text-gray-400 group-hover:text-gray-500">Registrar e acompanhar</p>
                                </div>
                                @if(request()->routeIs('coletas.*'))
                                <div class="ml-auto w-2 h-2 bg-green-600 rounded-full"></div>
                                @endif
                            </a>
                        </div>
                        @endif

                        <!-- Gestão de Entregas -->
                        <div class="mb-4">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Entregas</h3>
                            <a href="{{ route('motorista.dashboard') }}" class="group flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('motorista.dashboard') ? 'bg-green-50 text-green-700 border-r-2 border-green-600' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('motorista.dashboard') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200' }} transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium">Entregas</p>
                                    <p class="text-xs text-gray-400 group-hover:text-gray-500">Gerenciar entregas</p>
                                </div>
                                @if(request()->routeIs('motorista.dashboard'))
                                <div class="ml-auto w-2 h-2 bg-green-600 rounded-full"></div>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Menu do usuário -->
                <div class="p-4 border-t border-gray-100 mt-auto">
                    <div class="relative">
                        <div class="flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer" onclick="toggleUserMenu()">
                            <div class="relative">
                                <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-green-700 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr(auth()->user()->nome, 0, 2)) }}</span>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white shadow-sm"></div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-gray-900 font-medium text-sm">{{ Str::limit(auth()->user()->nome, 20) }}</p>
                                <p class="text-gray-500 text-xs">{{ auth()->user()->email }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" id="userMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Dropdown do usuário -->
                        <div id="userMenu" class="hidden absolute bottom-full left-0 right-0 mb-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-3 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <div class="text-left">
                                        <p class="font-medium">Sair do Sistema</p>
                                        <p class="text-xs text-gray-400">Encerrar sessão</p>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Overlay para mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

        <!-- Main content -->
        <main class="flex-1 bg-gradient-to-br from-gray-50 to-gray-100 lg:ml-0 min-h-screen">
            <div class="p-2 sm:p-3 lg:p-4 pt-16 lg:pt-4">
                <!-- Alertas -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            const icon = document.getElementById('userMenuIcon');

            menu.classList.toggle('hidden');
            icon.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleMobileMenu() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            mobileMenuButton.addEventListener('click', toggleMobileMenu);
            overlay.addEventListener('click', toggleMobileMenu);

            // Fechar menu ao clicar fora
            document.addEventListener('click', function(event) {
                const userMenu = document.getElementById('userMenu');
                const userMenuButton = event.target.closest('[onclick="toggleUserMenu()"]');

                if (!userMenuButton && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                    document.getElementById('userMenuIcon').style.transform = 'rotate(0deg)';
                }
            });

            // Fechar sidebar mobile ao redimensionar para desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
