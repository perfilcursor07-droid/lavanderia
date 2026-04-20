<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class EstabelecimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Estabelecimento::query();

        // Filtros
        if ($request->has('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('razao_social', 'like', "%{$search}%")
                  ->orWhere('nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $orderBy = $request->get('order_by', 'razao_social');
        $orderDirection = $request->get('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $estabelecimentos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $estabelecimentos->items(),
            'pagination' => [
                'current_page' => $estabelecimentos->currentPage(),
                'last_page' => $estabelecimentos->lastPage(),
                'per_page' => $estabelecimentos->perPage(),
                'total' => $estabelecimentos->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cnpj' => 'required|string|unique:estabelecimentos,cnpj',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|max:9',
            'telefone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'contato_responsavel' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $estabelecimento = Estabelecimento::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Estabelecimento criado com sucesso!',
            'data' => $estabelecimento
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Estabelecimento $estabelecimento): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $estabelecimento
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Estabelecimento $estabelecimento): JsonResponse
    {
        $request->validate([
            'cnpj' => 'required|string|unique:estabelecimentos,cnpj,' . $estabelecimento->id,
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|max:9',
            'telefone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'contato_responsavel' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $estabelecimento->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Estabelecimento atualizado com sucesso!',
            'data' => $estabelecimento
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Estabelecimento $estabelecimento): JsonResponse
    {
        // Verificar se há coletas associadas
        if ($estabelecimento->coletas()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir este estabelecimento pois há coletas associadas.'
            ], 422);
        }

        $estabelecimento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estabelecimento excluído com sucesso!'
        ]);
    }

    /**
     * Buscar dados do CNPJ na API externa
     */
    public function buscarCnpj(Request $request): JsonResponse
    {
        $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);
        
        if (strlen($cnpj) !== 14) {
            return response()->json([
                'success' => false,
                'message' => 'CNPJ inválido'
            ], 400);
        }

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->get("https://minhareceita.org/{$cnpj}");
            
            if ($response->successful()) {
                $data = $response->json();

                // Formatar telefone a partir dos campos ddd_telefone_1 ou ddd_telefone_2
                $telefone = '';
                if (!empty($data['ddd_telefone_1'])) {
                    $tel = $data['ddd_telefone_1'];
                    if (strlen($tel) >= 10) {
                        $ddd = substr($tel, 0, 2);
                        $numero = substr($tel, 2);
                        if (strlen($numero) == 9) {
                            $telefone = "({$ddd}) " . substr($numero, 0, 5) . "-" . substr($numero, 5);
                        } else {
                            $telefone = "({$ddd}) " . substr($numero, 0, 4) . "-" . substr($numero, 4);
                        }
                    }
                } elseif (!empty($data['ddd_telefone_2'])) {
                    $tel = $data['ddd_telefone_2'];
                    if (strlen($tel) >= 10) {
                        $ddd = substr($tel, 0, 2);
                        $numero = substr($tel, 2);
                        if (strlen($numero) == 9) {
                            $telefone = "({$ddd}) " . substr($numero, 0, 5) . "-" . substr($numero, 5);
                        } else {
                            $telefone = "({$ddd}) " . substr($numero, 0, 4) . "-" . substr($numero, 4);
                        }
                    }
                }

                // Formatar CEP
                $cep = $data['cep'] ?? '';
                if (strlen($cep) == 8) {
                    $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'cnpj' => $cnpj,
                        'razao_social' => $data['razao_social'] ?? '',
                        'nome_fantasia' => $data['nome_fantasia'] ?? '',
                        'endereco' => $data['logradouro'] ?? '',
                        'numero' => $data['numero'] ?? '',
                        'complemento' => $data['complemento'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'cidade' => $data['municipio'] ?? '',
                        'estado' => $data['uf'] ?? '',
                        'cep' => $cep,
                        'telefone' => $telefone,
                        'email' => $data['email'] ?? '',
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'CNPJ não encontrado'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CNPJ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle status ativo/inativo
     */
    public function toggleStatus(Estabelecimento $estabelecimento): JsonResponse
    {
        $estabelecimento->ativo = !$estabelecimento->ativo;
        $estabelecimento->save();

        $status = $estabelecimento->ativo ? 'ativado' : 'desativado';
        
        return response()->json([
            'success' => true,
            'message' => "Estabelecimento {$status} com sucesso!",
            'data' => $estabelecimento
        ]);
    }

    /**
     * Listar estabelecimentos ativos (para selects)
     */
    public function ativos(): JsonResponse
    {
        $estabelecimentos = Estabelecimento::ativos()
            ->orderBy('razao_social')
            ->get(['id', 'razao_social', 'nome_fantasia', 'cnpj']);

        return response()->json([
            'success' => true,
            'data' => $estabelecimentos
        ]);
    }
}
