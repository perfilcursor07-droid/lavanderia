<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Confirmação de Recebimento - Lavanderia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Confirmação de Recebimento</h1>
            <p class="text-gray-600">Confirme o recebimento da sua lavanderia</p>
        </div>

        @if(!$empacotamento)
            <!-- Buscar por QR Code -->
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Buscar Empacotamento</h2>
                <form action="{{ route('confirmacao-cliente.index') }}" method="GET">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Código QR</label>
                        <input type="text" name="codigo" value="{{ $codigoQr }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Digite o código QR do empacotamento">
                    </div>
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        Buscar
                    </button>
                </form>
            </div>
        @else
            @if($empacotamento->status->nome === 'Confirmado pelo Cliente')
                <!-- Já Confirmado -->
                <div class="max-w-md mx-auto bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-xl font-bold text-green-900">Já Confirmado!</h2>
                    </div>
                    <p class="text-green-700 mb-4">Este empacotamento já foi confirmado pelo cliente.</p>
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="font-medium text-gray-900">{{ $empacotamento->codigo_qr }}</p>
                        <p class="text-sm text-gray-600">{{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                        @if($empacotamento->entrega && $empacotamento->entrega->data_confirmacao_recebimento)
                            <p class="text-xs text-gray-500">Confirmado em: {{ $empacotamento->entrega->data_confirmacao_recebimento->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            @elseif($empacotamento->status->nome !== 'Entregue')
                <!-- Não Entregue Ainda -->
                <div class="max-w-md mx-auto bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h2 class="text-xl font-bold text-yellow-900">Aguardando Entrega</h2>
                    </div>
                    <p class="text-yellow-700 mb-4">Este empacotamento ainda não foi entregue.</p>
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <p class="font-medium text-gray-900">{{ $empacotamento->codigo_qr }}</p>
                        <p class="text-sm text-gray-600">{{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                        <p class="text-xs text-gray-500">Status: {{ $empacotamento->status->nome }}</p>
                    </div>
                </div>
            @else
                <!-- Confirmar Recebimento -->
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Confirmar Recebimento</h2>
                    
                    <!-- Informações do Empacotamento -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="font-medium text-blue-900">{{ $empacotamento->codigo_qr }}</p>
                        <p class="text-sm text-blue-700">{{ $empacotamento->coleta->estabelecimento->razao_social }}</p>
                        @if($empacotamento->entrega)
                            <p class="text-xs text-blue-600">Entregue em: {{ $empacotamento->entrega->data_entrega->format('d/m/Y H:i') }}</p>
                            @if($empacotamento->entrega->nome_recebedor)
                                <p class="text-xs text-blue-600">Recebido por: {{ $empacotamento->entrega->nome_recebedor }}</p>
                            @endif
                        @endif
                    </div>
                    
                    <form id="formConfirmacao">
                        <input type="hidden" name="codigo_qr" value="{{ $empacotamento->codigo_qr }}">
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assinatura de Confirmação</label>
                            <div class="border border-gray-300 rounded-lg">
                                <canvas id="canvasAssinatura" width="400" height="150" class="w-full cursor-crosshair"></canvas>
                            </div>
                            <div class="flex justify-between mt-2">
                                <button type="button" onclick="limparAssinatura()" 
                                        class="text-sm text-gray-600 hover:text-gray-800">
                                    Limpar Assinatura
                                </button>
                                <span class="text-xs text-gray-500">Desenhe sua assinatura acima</span>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                            Confirmar Recebimento
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>

    <!-- Modal de Sucesso -->
    <div id="modalSucesso" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
                <div class="text-center">
                    <svg class="w-16 h-16 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmado!</h3>
                    <p class="text-gray-600 mb-4">Seu recebimento foi confirmado com sucesso.</p>
                    <button onclick="fecharModal()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variáveis globais para assinatura
        let canvas, ctx, isDrawing = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar canvas de assinatura se existir
            canvas = document.getElementById('canvasAssinatura');
            if (canvas) {
                ctx = canvas.getContext('2d');
                
                // Configurar canvas
                ctx.strokeStyle = '#000';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                
                // Event listeners para desenhar
                canvas.addEventListener('mousedown', startDrawing);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', stopDrawing);
                canvas.addEventListener('mouseout', stopDrawing);
                
                // Touch events para mobile
                canvas.addEventListener('touchstart', handleTouch);
                canvas.addEventListener('touchmove', handleTouch);
                canvas.addEventListener('touchend', stopDrawing);
            }
        });

        // Funções de assinatura
        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        function draw(e) {
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                             e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }

        function limparAssinatura() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        // Submit do formulário
        document.getElementById('formConfirmacao')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Verificar se há assinatura
            const imageData = canvas.toDataURL();
            const isCanvasBlank = !ctx.getImageData(0, 0, canvas.width, canvas.height).data.some(channel => channel !== 0);
            
            if (isCanvasBlank) {
                alert('Por favor, faça a assinatura');
                return;
            }
            
            const codigoQr = document.querySelector('input[name="codigo_qr"]').value;
            
            fetch('{{ route("confirmacao-cliente.confirmar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    codigo_qr: codigoQr,
                    assinatura_cliente: imageData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalSucesso').classList.remove('hidden');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao confirmar recebimento');
            });
        });

        function fecharModal() {
            location.reload();
        }
    </script>
</body>
</html>
