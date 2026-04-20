<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empacotamento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motorista</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Saída</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Entrega</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo Entrega</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmação</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($entregas as $entrega)
            @php
                $tempoEntrega = null;
                $performance = 'N/A';
                
                if ($entrega->data_saida && $entrega->data_entrega) {
                    $minutos = \Carbon\Carbon::parse($entrega->data_saida)->diffInMinutes($entrega->data_entrega);
                    if ($minutos < 60) {
                        $tempoEntrega = $minutos . 'm';
                        $performance = $minutos <= 30 ? 'Excelente' : ($minutos <= 60 ? 'Bom' : 'Regular');
                    } else {
                        $horas = floor($minutos / 60);
                        $mins = $minutos % 60;
                        $tempoEntrega = $horas . 'h ' . $mins . 'm';
                        $performance = $minutos <= 120 ? 'Regular' : 'Lento';
                    }
                }
                
                $confirmado = $entrega->status->nome === 'Confirmado pelo Cliente';
                
                $performanceColor = match($performance) {
                    'Excelente' => 'bg-green-100 text-green-800',
                    'Bom' => 'bg-blue-100 text-blue-800',
                    'Regular' => 'bg-yellow-100 text-yellow-800',
                    'Lento' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800'
                };
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $entrega->empacotamento->codigo_qr }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        {{ $entrega->motoristaEntrega->nome ?? $entrega->motoristaSaida->nome ?? 'N/A' }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($entrega->data_saida)
                        <div class="text-sm text-gray-900">{{ $entrega->data_saida->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $entrega->data_saida->format('H:i') }}</div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($entrega->data_entrega)
                        <div class="text-sm text-gray-900">{{ $entrega->data_entrega->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $entrega->data_entrega->format('H:i') }}</div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($tempoEntrega)
                        <div class="text-sm text-gray-900">{{ $tempoEntrega }}</div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($confirmado)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Confirmado
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Pendente
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $performanceColor }}">
                        {{ $performance }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    Nenhuma entrega encontrada no período selecionado
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
