@extends('layouts.app')

@section('title', 'Editar Usuário - Sistema de Gestão de Lavanderia')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('usuarios.index') }}" 
               class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Usuário</h1>
                <p class="mt-2 text-gray-600">Atualize as informações do usuário {{ $usuario->nome }}</p>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Informações Pessoais -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Pessoais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo *
                        </label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome', $usuario->nome) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nome') border-red-500 @enderror"
                               required>
                        @error('nome')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CPF -->
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                            CPF
                        </label>
                        <input type="text" 
                               id="cpf" 
                               name="cpf" 
                               value="{{ old('cpf', $usuario->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $usuario->cpf) : '') }}"
                               placeholder="000.000.000-00"
                               maxlength="14"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cpf') border-red-500 @enderror">
                        @error('cpf')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $usuario->email) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefone -->
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone
                        </label>
                        <input type="text" 
                               id="telefone" 
                               name="telefone" 
                               value="{{ old('telefone', $usuario->telefone) }}"
                               placeholder="(00) 00000-0000"
                               maxlength="15"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefone') border-red-500 @enderror">
                        @error('telefone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Acesso ao Sistema -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acesso ao Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Senha -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nova Senha
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Deixe em branco para manter a senha atual</p>
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Nova Senha
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Nível de Acesso -->
                    <div>
                        <label for="nivel_acesso_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Nível de Acesso *
                        </label>
                        <select id="nivel_acesso_id" 
                                name="nivel_acesso_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nivel_acesso_id') border-red-500 @enderror"
                                required>
                            <option value="">Selecione um nível</option>
                            @foreach($niveisAcesso as $nivel)
                                <option value="{{ $nivel->id }}" {{ old('nivel_acesso_id', $usuario->nivel_acesso_id) == $nivel->id ? 'selected' : '' }}>
                                    {{ $nivel->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('nivel_acesso_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   id="ativo" 
                                   name="ativo" 
                                   value="1"
                                   {{ old('ativo', $usuario->ativo) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        </div>
                        <div class="ml-3">
                            <label for="ativo" class="text-sm font-medium text-gray-700">
                                Usuário Ativo
                            </label>
                            <p class="text-sm text-gray-500">Usuário pode fazer login no sistema</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Adicionais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Data de Cadastro
                        </label>
                        <p class="text-sm text-gray-900">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Último Login
                        </label>
                        <p class="text-sm text-gray-900">{{ $usuario->ultimo_login ? $usuario->ultimo_login->format('d/m/Y H:i') : 'Nunca' }}</p>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('usuarios.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    Atualizar Usuário
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Máscara para CPF - Limita a 11 dígitos
    document.getElementById('cpf').addEventListener('input', function(e) {
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

    // Máscara para telefone - Limita a 11 dígitos
    document.getElementById('telefone').addEventListener('input', function(e) {
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
</script>
@endpush
@endsection