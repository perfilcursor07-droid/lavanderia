<?php

namespace App\Http\Controllers;

use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EstabelecimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estabelecimentos = Estabelecimento::orderBy('razao_social')->paginate(10);
        
        return view('estabelecimentos.index', compact('estabelecimentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('estabelecimentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Processar emails antes da validação - remover vazios
        $requestData = $request->all();
        if (isset($requestData['emails'])) {
            $requestData['emails'] = array_filter($requestData['emails'], function($email) {
                return !empty(trim($email));
            });
            $requestData['emails'] = array_values($requestData['emails']);
        }

        // Atualizar o request com os dados processados
        $request->merge($requestData);

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
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255',
            'contatos_responsaveis' => 'nullable|array',
            'contatos_responsaveis.*.nome' => 'nullable|string|max:255',
            'contatos_responsaveis.*.telefone' => 'nullable|string|max:20',
            'observacoes' => 'nullable|string',
        ], [
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado é obrigatório.',
            'estado.size' => 'O estado deve ter 2 caracteres.',
            'cep.required' => 'O CEP é obrigatório.',
            'telefone.required' => 'O telefone é obrigatório.',
            'emails.*.email' => 'O email deve ser válido.',
        ]);

        $data = $request->all();



        // Processar contatos responsáveis - remover vazios (ambos campos devem estar preenchidos ou ambos vazios)
        if (isset($data['contatos_responsaveis'])) {
            $data['contatos_responsaveis'] = array_filter($data['contatos_responsaveis'], function($contato) {
                $nome = trim($contato['nome'] ?? '');
                $telefone = trim($contato['telefone'] ?? '');
                // Manter contato apenas se ambos os campos estiverem preenchidos
                return !empty($nome) && !empty($telefone);
            });
            $data['contatos_responsaveis'] = array_values($data['contatos_responsaveis']); // Reindexar array
        }

        // Verificar se CNPJ já existe
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $data['cnpj']);
        $existente = Estabelecimento::where('cnpj', $cnpjLimpo)->first();

        if ($existente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este CNPJ já está cadastrado no sistema. Estabelecimento: ' . $existente->razao_social);
        }

        // Limpar CNPJ antes de salvar
        $data['cnpj'] = $cnpjLimpo;

        $estabelecimento = Estabelecimento::create($data);

        return redirect()->route('estabelecimentos.index')
            ->with('success', 'Estabelecimento cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);
        return view('estabelecimentos.show', compact('estabelecimento'));
    }

    /**
     * Mostrar formulário de configuração de preços
     */
    public function editPrecos($id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);
        
        return view('estabelecimentos.precos', compact('estabelecimento'));
    }

    /**
     * Atualizar preços do estabelecimento
     */
    public function updatePrecos(Request $request, $id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);

        $request->validate([
            'tipo_precificacao' => 'required|in:peso,peca',
            'preco_kg' => 'nullable|numeric|min:0',
            'preco_peca' => 'nullable|numeric|min:0',
        ]);

        $estabelecimento->update([
            'tipo_precificacao' => $request->tipo_precificacao,
            'preco_kg' => $request->preco_kg ?? 0,
            'preco_peca' => $request->preco_peca ?? 0,
            'observacoes_preco' => $request->observacoes_preco,
        ]);

        return redirect()->route('estabelecimentos.show', $estabelecimento->id)
            ->with('success', 'Preços atualizados com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);
        return view('estabelecimentos.edit', compact('estabelecimento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);

        // Processar emails antes da validação - remover vazios
        $requestData = $request->all();
        if (isset($requestData['emails'])) {
            $requestData['emails'] = array_filter($requestData['emails'], function($email) {
                return !empty(trim($email));
            });
            $requestData['emails'] = array_values($requestData['emails']);
        }

        // Atualizar o request com os dados processados
        $request->merge($requestData);

        $request->validate([
            'cnpj' => 'required|string|unique:estabelecimentos,cnpj,' . $id,
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
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255',
            'contatos_responsaveis' => 'nullable|array',
            'contatos_responsaveis.*.nome' => 'nullable|string|max:255',
            'contatos_responsaveis.*.telefone' => 'nullable|string|max:20',
            'observacoes' => 'nullable|string',
        ], [
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado é obrigatório.',
            'estado.size' => 'O estado deve ter 2 caracteres.',
            'cep.required' => 'O CEP é obrigatório.',
            'telefone.required' => 'O telefone é obrigatório.',
            'emails.*.email' => 'O email deve ser válido.',
        ]);

        $data = $request->all();

        // Processar emails - remover vazios
        if (isset($data['emails'])) {
            $data['emails'] = array_filter($data['emails'], function($email) {
                return !empty(trim($email));
            });
            $data['emails'] = array_values($data['emails']); // Reindexar array
        }

        // Processar contatos responsáveis - remover vazios
        if (isset($data['contatos_responsaveis'])) {
            $data['contatos_responsaveis'] = array_filter($data['contatos_responsaveis'], function($contato) {
                return !empty(trim($contato['nome'] ?? '')) && !empty(trim($contato['telefone'] ?? ''));
            });
            $data['contatos_responsaveis'] = array_values($data['contatos_responsaveis']); // Reindexar array
        }

        $estabelecimento->update($data);

        return redirect()->route('estabelecimentos.index')
            ->with('success', 'Estabelecimento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);
        
        // Verificar se há coletas associadas
        if ($estabelecimento->coletas()->count() > 0) {
            return redirect()->route('estabelecimentos.index')
                ->with('error', 'Não é possível excluir este estabelecimento pois há coletas associadas.');
        }
        
        $estabelecimento->delete();

        return redirect()->route('estabelecimentos.index')
            ->with('success', 'Estabelecimento excluído com sucesso!');
    }

    /**
     * Buscar dados do CNPJ na API externa
     */
    public function buscarCnpj(Request $request)
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
                        'emails' => !empty($data['email']) ? [$data['email']] : [],
                        'contatos_responsaveis' => [],
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
    public function toggleStatus($id)
    {
        $estabelecimento = Estabelecimento::findOrFail($id);
        $estabelecimento->ativo = !$estabelecimento->ativo;
        $estabelecimento->save();

        $status = $estabelecimento->ativo ? 'ativado' : 'desativado';
        
        return redirect()->route('estabelecimentos.index')
            ->with('success', "Estabelecimento {$status} com sucesso!");
    }
}
