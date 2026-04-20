@extends('layouts.app')

@section('title', 'Novo Tipo de Peça')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('tipos.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Novo Tipo de Peça</h1>
            <p class="text-gray-500 mt-1">Cadastre um novo tipo de peça no sistema</p>
        </div>
    </div>

    <!-- Formulário -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('tipos.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('nome') border-red-500 @enderror"
                    placeholder="Ex: Blusa, Camisa Social, etc.">
                @error('nome')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
                <select name="categoria" id="categoria" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('categoria') border-red-500 @enderror">
                    <option value="">Selecione uma categoria</option>
                    @foreach($categorias as $key => $label)
                        <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('categoria')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                <textarea name="descricao" id="descricao" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('descricao') border-red-500 @enderror"
                    placeholder="Descrição opcional do tipo de peça">{{ old('descricao') }}</textarea>
                @error('descricao')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('tipos.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
