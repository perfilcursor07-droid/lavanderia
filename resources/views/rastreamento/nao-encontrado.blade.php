@extends('layouts.public')

@section('title', 'Código não encontrado')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-white/20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Rastreamento</h1>
                        <div class="text-xs text-gray-600">Código: {{ $codigo }}</div>
                    </div>
                </div>
                <a href="{{ route('home') }}" 
                   class="text-gray-700 hover:bg-white px-3 py-2 rounded-lg transition-all duration-300 text-sm font-medium">
                    <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Início</span>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <!-- Ícone -->
            <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Código não encontrado
            </h1>

            <!-- Descrição -->
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                O código <strong>{{ $codigo }}</strong> não foi encontrado em nosso sistema. 
                Verifique se o código está correto ou entre em contato conosco.
            </p>

            <!-- Dicas -->
            <div class="bg-blue-50 rounded-2xl p-6 mb-8 text-left max-w-lg mx-auto">
                <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Possíveis causas:
                </h3>
                <ul class="text-blue-800 text-sm space-y-2">
                    <li class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                        <span>Código digitado incorretamente</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                        <span>Empacotamento ainda não foi criado</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                        <span>Código muito antigo (anterior ao sistema)</span>
                    </li>
                </ul>
            </div>

            <!-- Ações -->
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Fazer Nova Busca</span>
                </a>

                <a href="tel:(11) 99999-9999" 
                   class="inline-flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>Entrar em Contato</span>
                </a>
            </div>

            <!-- Informações de contato -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Precisa de ajuda?
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <h4 class="font-medium text-gray-900">Telefone</h4>
                        <p class="text-gray-600">(11) 99999-9999</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="font-medium text-gray-900">Email</h4>
                        <p class="text-gray-600">contato@212lavanderia.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
