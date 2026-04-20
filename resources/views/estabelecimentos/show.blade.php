@extends('layouts.app')

@section('title', 'Detalhes do Estabelecimento - Sistema de Gestão de Lavanderia')

@section('content')
@if(!isset($estabelecimento) || !$estabelecimento)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Erro:</strong> Estabelecimento não encontrado.
        <a href="{{ route('estabelecimentos.index') }}" class="underline">Voltar para listagem</a>
    </div>
@else
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
            <svg class="inline w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Detalhes do Estabelecimento
        </h1>
        <p class="text-sm text-gray-600">Informações completas do estabelecimento</p>
    </div>
    <div class="flex gap-2 mt-3 sm:mt-0">
        <a href="{{ route('estabelecimentos.precos', $estabelecimento->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Preços
        </a>
        <a href="{{ route('estabelecimentos.edit', $estabelecimento->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Editar
        </a>
        <a href="{{ route('estabelecimentos.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-xl transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar
        </a>
    </div>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @if($estabelecimento->ativo)
        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Estabelecimento Ativo
        </span>
    @else
        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Estabelecimento Inativo
        </span>
    @endif
</div>

<!-- Card Principal com Informações -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <!-- Header do Card -->
    <div class="bg-gradient-to-r from-primary-50 to-primary-100 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $estabelecimento->razao_social }}</h2>
                @if($estabelecimento->nome_fantasia)
                <p class="text-sm text-gray-600 mt-1">{{ $estabelecimento->nome_fantasia }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-700">CNPJ</p>
                <p class="text-lg font-mono text-gray-900">
                    @if(strlen($estabelecimento->cnpj) >= 14)
                        {{ $estabelecimento->cnpj_formatado }}
                    @else
                        {{ $estabelecimento->cnpj }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Conteúdo do Card -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Coluna Esquerda - Endereço -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Endereço
                </h3>

                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-gray-400 mt-1 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <div>
                            <p class="text-gray-900 font-medium">
                                {{ $estabelecimento->endereco }}, {{ $estabelecimento->numero }}
                            </p>
                            @if($estabelecimento->complemento)
                            <p class="text-gray-600 text-sm">{{ $estabelecimento->complemento }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-gray-700">{{ $estabelecimento->bairro }}</p>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-700">{{ $estabelecimento->cidade }}/{{ $estabelecimento->estado }}</p>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-700">CEP: {{ $estabelecimento->cep_formatado ?? $estabelecimento->cep }}</p>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita - Contatos -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    Contatos
                </h3>

                <!-- Telefone Principal -->
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Telefone Principal</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $estabelecimento->telefone }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $estabelecimento->telefone) }}"
                               class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Ligar
                            </a>
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $estabelecimento->telefone) }}"
                               target="_blank"
                               class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Emails -->
                @if($estabelecimento->emails && is_array($estabelecimento->emails) && count($estabelecimento->emails) > 0)
                <div class="bg-green-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-gray-700">
                            Emails
                            <span class="ml-1 px-2 py-0.5 bg-green-200 text-green-800 text-xs rounded-full">{{ count($estabelecimento->emails) }}</span>
                        </p>
                    </div>
                    <div class="space-y-2">
                        @foreach($estabelecimento->emails as $email)
                        <div class="flex items-center justify-between bg-white rounded p-2">
                            <span class="text-sm text-gray-900 truncate flex-1">{{ $email }}</span>
                            <a href="mailto:{{ $email }}"
                               class="ml-2 inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Email
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Contatos Responsáveis -->
                @if($estabelecimento->contatos_responsaveis && is_array($estabelecimento->contatos_responsaveis) && count($estabelecimento->contatos_responsaveis) > 0)
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-gray-700">
                            Contatos Responsáveis
                            <span class="ml-1 px-2 py-0.5 bg-purple-200 text-purple-800 text-xs rounded-full">{{ count($estabelecimento->contatos_responsaveis) }}</span>
                        </p>
                    </div>
                    <div class="space-y-3">
                        @foreach($estabelecimento->contatos_responsaveis as $contato)
                        <div class="bg-white rounded-lg p-3 border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $contato['nome'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $contato['telefone'] }}</p>
                                </div>
                                <div class="flex space-x-1">
                                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $contato['telefone']) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors duration-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </a>
                                    <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $contato['telefone']) }}"
                                       target="_blank"
                                       class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition-colors duration-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cards Adicionais -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Observações -->
    @if($estabelecimento->observacoes)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Observações
        </h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-700 whitespace-pre-line">{{ $estabelecimento->observacoes }}</p>
        </div>
    </div>
    @endif

    <!-- Informações do Sistema -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Informações do Sistema
        </h3>
        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Cadastrado em:</span>
                <span class="text-sm font-medium text-gray-900">{{ $estabelecimento->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Última atualização:</span>
                <span class="text-sm font-medium text-gray-900">{{ $estabelecimento->updated_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">ID do Sistema:</span>
                <span class="text-sm font-mono font-medium text-gray-900">#{{ $estabelecimento->id }}</span>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
