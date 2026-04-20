<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coleta;
use App\Models\Estabelecimento;
use App\Models\Empacotamento;
use App\Models\Entrega;

class AcompanhamentoPublicoController extends Controller
{
    /**
     * Página inicial pública para acompanhamento
     */
    public function index()
    {
        return view('publico.acompanhamento.index');
    }

    /**
     * Buscar coletas por CNPJ ou número da coleta
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'busca' => 'required|string|min:3|max:50',
        ], [
            'busca.required' => 'Digite o CNPJ ou número da coleta para consultar.',
            'busca.min' => 'Digite pelo menos 3 caracteres.',
            'busca.max' => 'Máximo 50 caracteres.',
        ]);

        $termoBusca = $request->busca;
        $coletas = collect();

        // Limpar termo de busca (remover pontos, barras, hífens)
        $termoLimpo = preg_replace('/[^0-9A-Za-z]/', '', $termoBusca);

        // Buscar por número da coleta (exato ou parcial)
        $coletasPorNumero = Coleta::with([
            'estabelecimento', 
            'status', 
            'empacotamentos.status',
            'empacotamentos.entrega'
        ])
        ->where('numero_coleta', 'LIKE', "%{$termoBusca}%")
        ->get();

        $coletas = $coletas->merge($coletasPorNumero);

        // Buscar por CNPJ do estabelecimento
        if (strlen($termoLimpo) >= 8) { // CNPJ tem pelo menos 8 dígitos
            $estabelecimentos = Estabelecimento::where(function($query) use ($termoLimpo, $termoBusca) {
                $query->where('cnpj', 'LIKE', "%{$termoLimpo}%")
                      ->orWhere('cnpj', 'LIKE', "%{$termoBusca}%");
            })->get();

            foreach ($estabelecimentos as $estabelecimento) {
                $coletasEstabelecimento = Coleta::with([
                    'estabelecimento', 
                    'status', 
                    'empacotamentos.status',
                    'empacotamentos.entrega'
                ])
                ->where('estabelecimento_id', $estabelecimento->id)
                ->orderBy('created_at', 'desc')
                ->limit(10) // Limitar para não sobrecarregar
                ->get();

                $coletas = $coletas->merge($coletasEstabelecimento);
            }
        }

        // Remover duplicatas e ordenar
        $coletas = $coletas->unique('id')->sortByDesc('created_at');

        if ($coletas->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Nenhuma coleta encontrada para "' . $termoBusca . '". Verifique o CNPJ ou número da coleta.');
        }

        return view('publico.acompanhamento.resultados', compact('coletas', 'termoBusca'));
    }

    /**
     * Detalhes de uma coleta específica
     */
    public function detalhes($id)
    {
        $coleta = Coleta::with([
            'estabelecimento',
            'status',
            'pecas.tipo',
            'pesagens.usuario',
            'empacotamentos.status',
            'empacotamentos.entrega.motoristaSaida',
            'empacotamentos.entrega.motoristaEntrega'
        ])->findOrFail($id);

        // Calcular progresso da coleta
        $progresso = $this->calcularProgressoColeta($coleta);

        return view('publico.acompanhamento.detalhes', compact('coleta', 'progresso'));
    }

    /**
     * Calcular progresso da coleta em porcentagem
     */
    private function calcularProgressoColeta($coleta)
    {
        // Usar mesmo sistema de 5 etapas da página acompanhar-coletas
        $progresso = [
            'coleta' => ['concluida' => true], // Sempre concluída se existe
            'pesagem' => ['concluida' => $coleta->pesagens->count() > 0],
            'empacotamento' => ['concluida' => $coleta->empacotamentos->isNotEmpty()],
            'entrega' => ['concluida' => false],
            'confirmacao_cliente' => ['concluida' => false]
        ];

        // Verificar entrega e confirmação
        if ($coleta->empacotamentos->isNotEmpty()) {
            $empacotamento = $coleta->empacotamentos->first();
            $statusEmpacotamento = $empacotamento->status->nome;
            
            // Entrega concluída se empacotamento está pronto, em trânsito ou entregue
            if (in_array($statusEmpacotamento, ['Pronto para motorista', 'Em trânsito', 'Entregue'])) {
                $progresso['entrega']['concluida'] = true;
            }
            
            // Confirmação concluída se está entregue
            if ($statusEmpacotamento === 'Entregue') {
                $progresso['confirmacao_cliente']['concluida'] = true;
            }
        }

        // Calcular percentual: cada etapa vale 20%
        $etapasConcluidas = collect($progresso)->where('concluida', true)->count();
        $percentual = round(($etapasConcluidas / 5) * 100);

        // Determinar status atual baseado na última etapa concluída
        $statusAtual = $coleta->status->nome;
        if ($progresso['confirmacao_cliente']['concluida']) {
            $statusAtual = 'Entregue';
        } elseif ($progresso['entrega']['concluida']) {
            $statusAtual = 'Em trânsito';
        } elseif ($progresso['empacotamento']['concluida']) {
            $statusAtual = 'Empacotada';
        } elseif ($progresso['pesagem']['concluida']) {
            $statusAtual = 'Pesada';
        }

        return [
            'porcentagem' => $percentual,
            'status_atual' => $statusAtual,
            'etapas' => $this->getEtapasColeta($coleta),
            'progresso' => $progresso
        ];
    }

    /**
     * Obter etapas detalhadas da coleta
     */
    private function getEtapasColeta($coleta)
    {
        $etapas = [];

        // 1. Coleta Agendada
        $etapas[] = [
            'titulo' => 'Coleta Agendada',
            'descricao' => 'Sua coleta foi agendada e está na programação',
            'concluida' => true,
            'data' => $coleta->created_at,
            'icone' => 'calendar'
        ];

        // 2. Coleta em Andamento
        if (in_array($coleta->status->nome, ['Em andamento', 'Concluída', 'Empacotada'])) {
            $etapas[] = [
                'titulo' => 'Coleta em Andamento',
                'descricao' => 'Nossa equipe está realizando a coleta das peças',
                'concluida' => true,
                'data' => $coleta->updated_at,
                'icone' => 'truck'
            ];
        }

        // 3. Coleta Concluída
        if (in_array($coleta->status->nome, ['Concluída', 'Empacotada'])) {
            $etapas[] = [
                'titulo' => 'Coleta Concluída',
                'descricao' => 'Todas as peças foram coletadas e estão em nossa unidade',
                'concluida' => true,
                'data' => $coleta->updated_at,
                'icone' => 'check-circle'
            ];
        }

        // 4. Pesagem (se houver)
        if ($coleta->pesagens->isNotEmpty()) {
            $pesagem = $coleta->pesagens->first();
            $etapas[] = [
                'titulo' => 'Pesagem Realizada',
                'descricao' => "Peças pesadas: {$pesagem->peso}kg ({$pesagem->quantidade} peças)",
                'concluida' => true,
                'data' => $pesagem->data_pesagem,
                'icone' => 'scale'
            ];
        }

        // 5. Empacotamento
        if ($coleta->empacotamentos->isNotEmpty()) {
            $empacotamento = $coleta->empacotamentos->first();
            $etapas[] = [
                'titulo' => 'Empacotamento Concluído',
                'descricao' => 'Suas peças foram empacotadas e estão prontas',
                'concluida' => true,
                'data' => $empacotamento->data_empacotamento,
                'icone' => 'package'
            ];

            // 6. Pronto para Motorista
            if (in_array($empacotamento->status->nome, ['Pronto para motorista', 'Em trânsito', 'Entregue'])) {
                $etapas[] = [
                    'titulo' => 'Pronto para Entrega',
                    'descricao' => 'Empacotamento está aguardando saída para entrega',
                    'concluida' => true,
                    'data' => $empacotamento->updated_at,
                    'icone' => 'clock'
                ];
            }

            // 7. Em Trânsito
            if (in_array($empacotamento->status->nome, ['Em trânsito', 'Entregue']) && $empacotamento->entrega) {
                $entrega = $empacotamento->entrega;
                $etapas[] = [
                    'titulo' => 'Em Trânsito',
                    'descricao' => 'Motorista saiu para entrega das suas peças',
                    'concluida' => true,
                    'data' => $entrega->data_saida,
                    'icone' => 'truck'
                ];
            }

            // 8. Entregue
            if ($empacotamento->status->nome === 'Entregue' && $empacotamento->entrega) {
                $entrega = $empacotamento->entrega;
                $etapas[] = [
                    'titulo' => 'Entregue',
                    'descricao' => 'Suas peças foram entregues com sucesso',
                    'concluida' => true,
                    'data' => $entrega->data_entrega,
                    'icone' => 'check'
                ];
            }
        }

        return $etapas;
    }
}