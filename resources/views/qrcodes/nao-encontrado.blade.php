@extends('layouts.public')

@section('title', 'QR Code não encontrado')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">QR Code não encontrado</h1>
            <p class="text-gray-600 text-sm mb-4">
                O código <span class="font-mono font-bold text-red-600">{{ $codigo }}</span> não foi encontrado no sistema.
            </p>
            <p class="text-gray-500 text-xs mb-6">Verifique se o código está correto ou entre em contato com a lavanderia.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar ao início
            </a>
        </div>
    </div>
</div>
@endsection
