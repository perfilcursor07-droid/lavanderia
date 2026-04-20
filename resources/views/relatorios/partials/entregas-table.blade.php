<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empacotamento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estabelecimento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motorista</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Saída</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Entrega</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($entregas as $entrega)
            @php
                $tempoEntrega = null;
                if ($entrega->data_saida && $entrega->data_entrega) {
                    $minutos = \Carbon\Carbon::parse($entrega->data_saida)->diffInMinutes($entrega->data_entrega);
                    if ($minutos < 60) {
                        $tempoEntrega = $minutos . 'm';
                    } else {
                        $horas = floor($minutos / 60);
                        $mins = $minutos % 60;
                        $tempoEntrega = $horas . 'h ' . $mins . 'm';
                    }
                }
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $entrega->empacotamento->codigo_qr }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $entrega->empacotamento->coleta->estabelecimento->razao_social }}</div>
                    @if($entrega->empacotamento->coleta->estabelecimento->nome_fantasia)
                        <div class="text-xs text-gray-500">{{ $entrega->empacotamento->coleta->estabelecimento->nome_fantasia }}</div>
                    @endif
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          style="background-color: {{ $entrega->status->cor }}20; color: {{ $entrega->status->cor }};">
                        {{ $entrega->status->nome }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($tempoEntrega)
                        <div class="text-sm text-gray-900">{{ $tempoEntrega }}</div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
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
