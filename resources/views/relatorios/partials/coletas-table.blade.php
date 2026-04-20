<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estabelecimento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progresso</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($coletas as $coleta)
            @php
                // Calcular progresso
                $progresso = [
                    'coleta' => ['concluida' => true],
                    'pesagem' => ['concluida' => $coleta->pesagens->count() > 0],
                    'empacotamento' => ['concluida' => $coleta->empacotamento !== null],
                    'entrega' => ['concluida' => $coleta->empacotamento && $coleta->empacotamento->entrega && in_array($coleta->empacotamento->entrega->status->nome, ['Em trânsito', 'Entregue', 'Confirmado pelo Cliente'])],
                    'confirmacao_cliente' => ['concluida' => $coleta->empacotamento && $coleta->empacotamento->entrega && in_array($coleta->empacotamento->entrega->status->nome, ['Entregue', 'Confirmado pelo Cliente'])]
                ];
                $etapasConcluidas = collect($progresso)->where('concluida', true)->count();
                $percentual = round(($etapasConcluidas / 5) * 100);
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $coleta->numero_coleta }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $coleta->estabelecimento->razao_social }}</div>
                    @if($coleta->estabelecimento->nome_fantasia)
                        <div class="text-xs text-gray-500">{{ $coleta->estabelecimento->nome_fantasia }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $coleta->created_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $coleta->created_at->format('H:i') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ number_format($coleta->peso_total, 2) }}kg</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          style="background-color: {{ $coleta->status->cor }}20; color: {{ $coleta->status->cor }};">
                        {{ $coleta->status->nome }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentual }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600">{{ $percentual }}%</span>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Nenhuma coleta encontrada no período selecionado
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
