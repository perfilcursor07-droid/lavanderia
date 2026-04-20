<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestão de Lavanderia')</title>

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

    <!-- Script para aplicar estado do sidebar antes da renderização -->
    <script>
        // Aplicar estado do sidebar imediatamente para evitar flash
        (function() {
            if (window.innerWidth >= 1024 && localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }
        })();
    </script>

    <style>
        /* Aplicar estado colapsado imediatamente se a classe estiver presente */
        .sidebar-collapsed-init #sidebar {
            width: 5rem !important;
        }
        
        .sidebar-collapsed-init #sidebar .sidebar-text {
            display: none !important;
        }
        
        .sidebar-collapsed-init #sidebar h3 {
            display: none !important;
        }
        
        /* Desabilitar transição no carregamento inicial */
        .sidebar-no-transition {
            transition: none !important;
        }
        
        .sidebar-no-transition * {
            transition: none !important;
        }
        
        /* Sidebar collapsed state */
        #sidebar {
            width: 16rem; /* 256px padrão */
            transition: width 0.3s ease-in-out;
        }
        
        #sidebar.sidebar-collapsed {
            width: 5rem; /* 80px quando colapsado */
        }
        
        /* Esconder textos quando colapsado */
        #sidebar.sidebar-collapsed .sidebar-text {
            display: none;
        }
        
        /* Mostrar logo colapsado */
        #sidebar.sidebar-collapsed .sidebar-collapsed-logo {
            display: block !important;
        }
        
        /* Esconder títulos de seção quando colapsado */
        #sidebar.sidebar-collapsed h3 {
            display: none;
        }
        
        /* Centralizar itens quando colapsado */
        #sidebar.sidebar-collapsed .menu-item {
            justify-content: center;
            padding: 0.5rem !important;
        }
        
        /* Ajustar logo quando colapsado */
        #sidebar.sidebar-collapsed .logo-container {
            justify-content: center;
        }
        
        /* Ajustar espaçamento quando colapsado */
        #sidebar.sidebar-collapsed .menu-section {
            padding: 0.5rem;
        }
        
        /* Ajustar menu do usuário quando colapsado */
        #sidebar.sidebar-collapsed .user-menu-container {
            padding: 1rem 0.5rem;
        }
        
        #sidebar.sidebar-collapsed .user-menu-container .flex {
            justify-content: center;
            padding: 0.5rem !important;
        }
        
        /* Ajustar botão de colapsar quando colapsado */
        #sidebar.sidebar-collapsed .logo-container button {
            position: absolute;
            top: 1rem;
            right: 0.5rem;
        }
        
        /* Transições suaves */
        .sidebar-text {
            transition: opacity 0.2s ease-in-out;
        }
        
        /* Melhorar tooltips quando colapsado */
        #sidebar.sidebar-collapsed [title]:hover::after {
            content: attr(title);
            position: absolute;
            left: calc(100% + 0.5rem);
            top: 50%;
            transform: translateY(-50%);
            padding: 0.5rem 0.75rem;
            background: #1f2937;
            color: white;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 1000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            pointer-events: none;
        }
        
        /* Seta do tooltip */
        #sidebar.sidebar-collapsed [title]:hover::before {
            content: '';
            position: absolute;
            left: calc(100% + 0.25rem);
            top: 50%;
            transform: translateY(-50%);
            border: 4px solid transparent;
            border-right-color: #1f2937;
            z-index: 1000;
            pointer-events: none;
        }
        
        /* Estilo profissional dos ícones */
        .menu-icon-wrapper {
            position: relative;
            transition: all 0.2s ease-in-out;
            flex-shrink: 0;
        }
        
        /* Ícone ativo - azul com sombra */
        .menu-item-active .menu-icon-wrapper {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35), 0 2px 4px rgba(59, 130, 246, 0.25) !important;
        }
        
        .menu-item-active .menu-icon-wrapper svg {
            color: white !important;
        }
        
        /* Hover nos ícones inativos */
        .menu-item:hover .menu-icon-wrapper:not(.menu-item-active .menu-icon-wrapper) {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-1px);
        }
        
        /* Ícones inativos - cinza suave */
        .menu-icon-wrapper {
            background: #f8fafc;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Animação suave */
        .menu-icon-wrapper svg {
            transition: all 0.2s ease-in-out;
        }
        
        /* Garantir que os ícones fiquem centralizados */
        .menu-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

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
        <nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 shadow-lg transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out sidebar-no-transition">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="px-2.5 py-2 border-b border-gray-200 bg-white">
                    <div class="flex items-center justify-between logo-container">
                        <div class="flex items-center space-x-2 sidebar-text">
                            <div class="relative">
                                <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <div class="absolute -top-1.5 -right-1.5 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white shadow-sm"></div>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 leading-tight">212lavanderia</h2>
                                <p class="text-gray-500 text-xs">Sistema de Gestão</p>
                            </div>
                        </div>
                        <!-- Logo quando colapsado -->
                        <div class="hidden sidebar-collapsed-logo">
                            <div class="relative">
                                <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <div class="absolute -top-1.5 -right-1.5 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white shadow-sm"></div>
                            </div>
                        </div>
                        <!-- Botão de colapsar (apenas desktop) -->
                        <button onclick="toggleSidebar()" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors" title="Expandir/Recolher menu">
                            <svg id="collapseIcon" class="w-4 h-4 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Menu Principal -->
                <div class="flex-1 px-3 py-3 overflow-y-auto bg-white text-sm menu-section">
                    <div class="space-y-2">
                        @php
                            $nivelAcesso = auth()->user()->nivelAcesso->nome ?? null;
                            $isAdmin = in_array($nivelAcesso, ['Administrador', 'Gestor']);
                            $isMotorista = $nivelAcesso === 'Motorista';
                            $isPesagem = $nivelAcesso === 'Pesagem';
                            $isEmpacotamento = $nivelAcesso === 'Empacotamento';
                        @endphp

                        <!-- Dashboard e Acompanhamento - Apenas Admin e Gestor -->
                        @if($isAdmin)
                        <div class="mb-4">
                            <h3 class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 px-2">Principal</h3>
                            <div class="space-y-1">
                                <a href="{{ route('painel') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('painel') ? 'menu-item-active bg-blue-50' : '' }}" title="Dashboard">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('painel') ? '' : 'bg-gray-50 text-gray-600' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Dashboard</p>
                                        <p class="text-[10px] text-gray-500">Visão geral</p>
                                    </div>
                                </a>
                                <a href="{{ route('acompanhar-coletas') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('acompanhar-coletas') ? 'menu-item-active bg-blue-50' : '' }}" title="Acompanhar Coletas">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('acompanhar-coletas') ? '' : 'bg-gray-50 text-gray-600' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Acompanhar</p>
                                        <p class="text-[10px] text-gray-500">Monitorar coletas</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Operações -->
                        <div class="mb-4">
                            <h3 class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5 px-2">
                                @if($isMotorista) Minhas Operações
                                @elseif($isPesagem) Pesagem
                                @elseif($isEmpacotamento) Empacotamento
                                @else Operações
                                @endif
                            </h3>
                            <div class="space-y-1">
                                <!-- Estabelecimentos - Apenas Admin e Gestor -->
                                @if($isAdmin && auth()->user()->temPermissao('estabelecimentos.visualizar'))
                                <a href="{{ route('estabelecimentos.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('estabelecimentos.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Estabelecimentos">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('estabelecimentos.*') ? '' : 'bg-gray-50 text-gray-600' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Estabelecimentos</p><p class="text-[10px] text-gray-500">Gerenciar clientes</p>
                                    </div>
                                </a>
                                @endif

                                <!-- Coletas - Admin, Gestor, Motorista e Pesagem -->
                                @if(($isAdmin || $isMotorista || $isPesagem) && auth()->user()->temPermissao('coletas.visualizar'))
                                <a href="{{ route('coletas.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('coletas.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Coletas">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('coletas.*') ? '' : 'bg-gray-50 text-gray-600' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Coletas</p><p class="text-[10px] text-gray-500">Agendar e gerenciar</p>
                                    </div>
                                </a>
                                @endif

                                <!-- Pesagem - Admin, Gestor e Pesagem -->
                                @if(($isAdmin || $isPesagem) && auth()->user()->temPermissao('pesagem.visualizar'))
                                <a href="{{ route('pesagem.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('pesagem.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Pesagem">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('pesagem.*') ? '' : 'bg-gray-50 text-gray-500' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Pesagem</p><p class="text-[10px] text-gray-500">Controle de peso</p>
                                    </div>
                                    @if(request()->routeIs('pesagem.*'))
                                    <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </a>
                                @endif

                                <!-- Empacotamento - Admin, Gestor e Empacotamento -->
                                @if(($isAdmin || $isEmpacotamento) && auth()->user()->temPermissao('empacotamento.visualizar'))
                                <a href="{{ route('empacotamento.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('empacotamento.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Empacotamento">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('empacotamento.*') ? '' : 'bg-gray-50 text-gray-500' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Empacotamento</p><p class="text-[10px] text-gray-500">Finalizar pedidos</p>
                                    </div>
                                    @if(request()->routeIs('empacotamento.*'))
                                    <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </a>
                                @endif

                                <!-- Entregas - Admin, Gestor e Motorista -->
                                @if(($isAdmin || $isMotorista) && auth()->user()->temPermissao('motorista.visualizar'))
                                <a href="{{ route('motorista.dashboard') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all duration-200 {{ request()->routeIs('motorista.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Entregas">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-lg {{ request()->routeIs('motorista.*') ? '' : 'bg-gray-50 text-gray-500' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 leading-tight sidebar-text">
                                        <p class="font-semibold text-xs">Entregas</p><p class="text-[10px] text-gray-500">Gestão de entregas</p>
                                    </div>
                                    @if(request()->routeIs('motorista.*'))
                                    <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </a>
                                @endif
                            </div>
                        </div>



                        <!-- Administração -->
                        @if(auth()->user()->temPermissao('usuarios.visualizar') || auth()->user()->temPermissao('relatorios.visualizar') || auth()->user()->temPermissao('tipos.visualizar'))
                        <div class="mb-3">
                            <h3 class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5 px-2">Administração</h3>
                            <div class="space-y-1">
                                @if(auth()->user()->temPermissao('tipos.visualizar'))
                                <a href="{{ route('tipos.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all duration-200 {{ request()->routeIs('tipos.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Tipos de Peças">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-xl {{ request()->routeIs('tipos.*') ? '' : 'bg-gray-50 text-gray-500' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 sidebar-text">
                                        <p class="font-semibold text-sm">Tipos de Peças</p>
                                        <p class="text-xs text-gray-500">Gerenciar tipos</p>
                                    </div>
                                    @if(request()->routeIs('tipos.*'))
                                    <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </a>
                                @endif

                                @if(auth()->user()->temPermissao('usuarios.visualizar'))
                                <a href="{{ route('usuarios.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all duration-200 {{ request()->routeIs('usuarios.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Usuários">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-xl {{ request()->routeIs('usuarios.*') ? '' : 'bg-gray-50 text-gray-500' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 sidebar-text">
                                        <p class="font-semibold text-sm">Usuários</p>
                                        <p class="text-xs text-gray-500">Gerenciar usuários</p>
                                    </div>
                                    @if(request()->routeIs('usuarios.*'))
                                    <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </a>
                                @endif

                                @if(auth()->user()->temPermissao('relatorios.visualizar'))
                                <a href="{{ route('relatorios.index') }}" class="menu-item group flex items-center px-2.5 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all duration-200 {{ request()->routeIs('relatorios.*') ? 'menu-item-active bg-blue-50' : '' }}" title="Relatórios">
                                    <div class="menu-icon-wrapper w-8 h-8 rounded-xl {{ request()->routeIs('relatorios.*') ? '' : 'bg-gray-50 text-gray-500' }} transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2.5 sidebar-text">
                                        <p class="font-semibold text-sm">Relatórios</p>
                                        <p class="text-xs text-gray-500">Análises e estatísticas</p>
                                    </div>
                                    @if(request()->routeIs('relatorios.*'))
                                    <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Menu do usuário -->
                <div class="sticky bottom-0 p-3 border-t border-gray-200 bg-white user-menu-container shadow-lg">
                    <div class="relative">
                        <div class="flex items-center px-2.5 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 cursor-pointer" onclick="toggleUserMenu()">
                            <div class="relative">
                                <div class="w-7 h-7 bg-gray-900 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-semibold text-[10px]">{{ strtoupper(substr(auth()->user()->nome, 0, 2)) }}</span>
                                </div>
                                <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></div>
                            </div>
                            <div class="ml-2.5 flex-1 leading-tight sidebar-text">
                                <p class="text-gray-900 font-semibold text-xs">{{ Str::limit(auth()->user()->nome, 20) }}</p>
                                <p class="text-gray-500 text-[10px]">{{ Str::limit(auth()->user()->email, 22) }}</p>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 sidebar-text" id="userMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Dropdown do usuário -->
                        <div id="userMenu" class="hidden absolute bottom-full left-0 right-0 mb-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <a href="#" class="flex items-center px-2.5 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-xs">Meu Perfil</p>
                                    <p class="text-[10px] text-gray-400">Configurações</p>
                                </div>
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-2.5 py-2 text-xs text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <div class="text-left leading-tight">
                                        <p class="font-medium">Sair</p>
                                        <p class="text-[10px] text-gray-400">Encerrar sessão</p>
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
            <div class="p-3 sm:p-4 lg:p-8 pt-16 lg:pt-8">
                <!-- Alertas -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-green-800 font-semibold text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-red-800 font-semibold text-sm">{{ session('error') }}</p>
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

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const collapseIcon = document.getElementById('collapseIcon');
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            
            if (isCollapsed) {
                sidebar.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', 'false');
            } else {
                sidebar.classList.add('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', 'true');
            }
            
            collapseIcon.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(`submenu-${menuId}`);
            const chevron = document.getElementById(`chevron-${menuId}`);
            
            if (submenu && chevron) {
                submenu.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            }
        }

        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const collapseIcon = document.getElementById('collapseIcon');

            // Restaurar estado do sidebar do localStorage ANTES de remover a classe no-transition
            if (window.innerWidth >= 1024) {
                const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (sidebarCollapsed) {
                    sidebar.classList.add('sidebar-collapsed');
                    if (collapseIcon) {
                        collapseIcon.style.transform = 'rotate(180deg)';
                    }
                }
            }
            
            // Remover as classes que desabilitam transições após aplicar o estado
            setTimeout(() => {
                sidebar.classList.remove('sidebar-no-transition');
                document.documentElement.classList.remove('sidebar-collapsed-init');
            }, 50);

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




