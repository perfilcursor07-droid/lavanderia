<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anotacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AnotacaoController extends Controller
{
    /**
     * Listar anotações por módulo
     */
    public function index(Request $request)
    {
        $query = Anotacao::with('usuario')
                         ->doUsuario(Auth::id())
                         ->orderBy('created_at', 'desc');

        // Filtrar por módulo se especificado
        if ($request->filled('modulo')) {
            $query->porModulo($request->modulo);
        }

        // Filtrar por categoria se especificado
        if ($request->filled('categoria')) {
            $query->porCategoria($request->categoria);
        }

        // Filtrar por status se especificado
        if ($request->filled('resolvida')) {
            if ($request->resolvida === 'true') {
                $query->resolvidas();
            } else {
                $query->naoResolvidas();
            }
        }

        $anotacoes = $query->get();

        return response()->json([
            'success' => true,
            'anotacoes' => $anotacoes->map(function ($anotacao) {
                return [
                    'id' => $anotacao->id,
                    'modulo' => $anotacao->modulo,
                    'modulo_formatado' => $anotacao->modulo_formatado,
                    'pagina' => $anotacao->pagina,
                    'pagina_nome' => $anotacao->pagina_nome,
                    'categoria' => $anotacao->categoria,
                    'categoria_formatada' => $anotacao->categoria_formatada,
                    'categoria_icone' => $anotacao->categoria_icone,
                    'categoria_cor' => $anotacao->categoria_cor,
                    'texto' => $anotacao->texto,
                    'resolvida' => $anotacao->resolvida,
                    'data_formatada' => $anotacao->data_formatada,
                    'tempo_relativo' => $anotacao->tempo_relativo,
                    'usuario' => $anotacao->usuario->nome,
                ];
            })
        ]);
    }

    /**
     * Salvar nova anotação
     */
    public function store(Request $request)
    {
        $request->validate([
            'modulo' => 'required|string|max:50',
            'pagina' => 'nullable|string|max:100',
            'pagina_nome' => 'nullable|string|max:150',
            'categoria' => ['required', Rule::in(['melhorias', 'alteracoes', 'exclusoes'])],
            'texto' => 'required|string|max:500',
        ], [
            'modulo.required' => 'O módulo é obrigatório.',
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in' => 'Categoria inválida.',
            'texto.required' => 'O texto da anotação é obrigatório.',
            'texto.max' => 'O texto deve ter no máximo 500 caracteres.',
        ]);

        $anotacao = Anotacao::create([
            'usuario_id' => Auth::id(),
            'modulo' => $request->modulo,
            'pagina' => $request->pagina,
            'pagina_nome' => $request->pagina_nome,
            'categoria' => $request->categoria,
            'texto' => $request->texto,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anotação salva com sucesso!',
            'anotacao' => [
                'id' => $anotacao->id,
                'modulo' => $anotacao->modulo,
                'modulo_formatado' => $anotacao->modulo_formatado,
                'pagina' => $anotacao->pagina,
                'categoria' => $anotacao->categoria,
                'categoria_formatada' => $anotacao->categoria_formatada,
                'categoria_icone' => $anotacao->categoria_icone,
                'categoria_cor' => $anotacao->categoria_cor,
                'texto' => $anotacao->texto,
                'resolvida' => $anotacao->resolvida,
                'data_formatada' => $anotacao->data_formatada,
                'tempo_relativo' => $anotacao->tempo_relativo,
                'usuario' => $anotacao->usuario->nome,
            ]
        ]);
    }

    /**
     * Atualizar anotação
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'texto' => 'required|string|max:500',
        ], [
            'texto.required' => 'O texto da anotação é obrigatório.',
            'texto.max' => 'O texto deve ter no máximo 500 caracteres.',
        ]);

        $anotacao = Anotacao::where('id', $id)
                           ->doUsuario(Auth::id())
                           ->naoResolvidas()
                           ->firstOrFail();

        $anotacao->update([
            'texto' => $request->texto,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anotação atualizada com sucesso!',
            'anotacao' => [
                'id' => $anotacao->id,
                'modulo' => $anotacao->modulo,
                'modulo_formatado' => $anotacao->modulo_formatado,
                'pagina' => $anotacao->pagina,
                'categoria' => $anotacao->categoria,
                'categoria_formatada' => $anotacao->categoria_formatada,
                'categoria_icone' => $anotacao->categoria_icone,
                'categoria_cor' => $anotacao->categoria_cor,
                'texto' => $anotacao->texto,
                'resolvida' => $anotacao->resolvida,
                'data_formatada' => $anotacao->data_formatada,
                'tempo_relativo' => $anotacao->tempo_relativo,
                'usuario' => $anotacao->usuario->nome,
            ]
        ]);
    }

    /**
     * Excluir anotação
     */
    public function destroy($id)
    {
        $anotacao = Anotacao::where('id', $id)
                           ->doUsuario(Auth::id())
                           ->firstOrFail();

        $anotacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anotação excluída com sucesso!'
        ]);
    }

    /**
     * Marcar como resolvida
     */
    public function marcarResolvida(Request $request, $id)
    {
        $request->validate([
            'observacao' => 'nullable|string|max:500',
        ]);

        $anotacao = Anotacao::where('id', $id)
                           ->doUsuario(Auth::id())
                           ->firstOrFail();

        $anotacao->marcarComoResolvida($request->observacao);

        return response()->json([
            'success' => true,
            'message' => 'Anotação marcada como resolvida!'
        ]);
    }

    /**
     * Marcar como não resolvida
     */
    public function marcarNaoResolvida($id)
    {
        $anotacao = Anotacao::where('id', $id)
                           ->doUsuario(Auth::id())
                           ->firstOrFail();

        $anotacao->marcarComoNaoResolvida();

        return response()->json([
            'success' => true,
            'message' => 'Anotação marcada como não resolvida!'
        ]);
    }

    /**
     * Estatísticas das anotações
     */
    public function estatisticas()
    {
        $usuarioId = Auth::id();

        $stats = [
            'total' => Anotacao::doUsuario($usuarioId)->count(),
            'nao_resolvidas' => Anotacao::doUsuario($usuarioId)->naoResolvidas()->count(),
            'resolvidas' => Anotacao::doUsuario($usuarioId)->resolvidas()->count(),
            'por_categoria' => [
                'melhorias' => Anotacao::doUsuario($usuarioId)->porCategoria('melhorias')->count(),
                'alteracoes' => Anotacao::doUsuario($usuarioId)->porCategoria('alteracoes')->count(),
                'exclusoes' => Anotacao::doUsuario($usuarioId)->porCategoria('exclusoes')->count(),
            ],
            'por_modulo' => Anotacao::doUsuario($usuarioId)
                                   ->selectRaw('modulo, COUNT(*) as total')
                                   ->groupBy('modulo')
                                   ->pluck('total', 'modulo')
                                   ->toArray(),
        ];

        return response()->json([
            'success' => true,
            'estatisticas' => $stats
        ]);
    }

    /**
     * Relatório de anotações
     */
    public function relatorio(Request $request)
    {
        $query = Anotacao::with('usuario')
                         ->doUsuario(Auth::id());

        // Filtros opcionais
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        if ($request->filled('modulo')) {
            $query->porModulo($request->modulo);
        }

        $anotacoes = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'relatorio' => $anotacoes->groupBy('modulo')->map(function ($anotacoesPorModulo, $modulo) {
                return [
                    'modulo' => $modulo,
                    'modulo_formatado' => $anotacoesPorModulo->first()->modulo_formatado,
                    'total' => $anotacoesPorModulo->count(),
                    'por_categoria' => $anotacoesPorModulo->groupBy('categoria')->map(function ($items, $categoria) {
                        return [
                            'categoria' => $categoria,
                            'total' => $items->count(),
                            'anotacoes' => $items->values()
                        ];
                    })
                ];
            })->values()
        ]);
    }
}
