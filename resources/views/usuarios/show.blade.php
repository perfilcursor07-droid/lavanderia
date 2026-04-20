@extends('layouts.app')

@section('title', 'Visualizar Usuário - Sistema de Gestão de Lavanderia')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('usuarios.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $usuario->nome }}</h1>
                    <p class="mt-2 text-gray-600">Informações detalhadas do usuário</p>
                </div>
            </div>
            
            @if(auth()->user()->temPermissao('usuarios.editar'))
            <div class="flex space-x-3">
                <a href="{{ route('usuarios.edit', $usuario->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações Principais -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Dados Pessoais -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dados Pessoais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nome Completo</label>
                        <p class="text-gray-900">{{ $usuario->nome }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-gray-900">{{ $usuario->email }}</p>
                    </div>
                    
                    @if($usuario->cpf)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">CPF</label>
                        <p class="text-gray-900">{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $usuario->cpf) }}</p>
                    </div>
                    @endif
                    
                    @if($usuario->telefone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Telefone</label>
                        <p class="text-gray-900">{{ $usuario->telefone }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acesso ao Sistema -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acesso ao Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nível de Acesso</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $usuario->nivelAcesso->nome }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        @if($usuario->ativo)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Inativo
                            </span>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Data de Cadastro</label>
                        <p class="text-gray-900">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Último Login</label>
                        <p class="text-gray-900">{{ $usuario->ultimo_login ? $usuario->ultimo_login->format('d/m/Y H:i') : 'Nunca' }}</p>
                    </div>
                </div>
            </div>

            <!-- Permissões -->
            @if($usuario->nivelAcesso->permissoes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissões</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($usuario->nivelAcesso->permissoes as $permissao)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ ucfirst(str_replace('.', ' → ', $permissao)) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Avatar e Ações -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-2xl">{{ strtoupper(substr($usuario->nome, 0, 2)) }}</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $usuario->nome }}</h3>
                    <p class="text-sm text-gray-500">{{ $usuario->nivelAcesso->nome }}</p>
                </div>

                @if(auth()->user()->temPermissao('usuarios.editar') && $usuario->id !== auth()->id())
                <div class="mt-6 space-y-3">
                    <form method="POST" action="{{ route('usuarios.toggle-status', $usuario->id) }}">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2 {{ $usuario->ativo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors duration-200"
                                onclick="return confirm('Tem certeza que deseja {{ $usuario->ativo ? 'desativar' : 'ativar' }} este usuário?')">
                            {{ $usuario->ativo ? 'Desativar Usuário' : 'Ativar Usuário' }}
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Estatísticas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Coletas Criadas</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $usuario->coletas()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Empacotamentos</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $usuario->empacotamentos()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Entregas</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $usuario->entregas()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection