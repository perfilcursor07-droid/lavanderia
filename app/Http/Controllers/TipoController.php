<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use Illuminate\Http\Request;

class TipoController extends Controller
{
    /**
     * Lista de categorias disponíveis
     */
    private function getCategorias()
    {
        return [
            'roupa_cama' => 'Roupa de Cama',
            'roupa_banho' => 'Roupa de Banho',
            'vestuario' => 'Vestuário',
            'mesa_copa' => 'Mesa e Copa',
            'cortina' => 'Cortinas',
            'hospitalar' => 'Hospitalar',
            'hotelaria' => 'Hotelaria',
            'restaurante' => 'Restaurante',
            'uniforme' => 'Uniformes',
            'epi' => 'EPI / Segurança',
            'industrial' => 'Industrial',
        ];
    }

    /**
     * Lista de categorias para validação
     */
    private function getCategoriasKeys()
    {
        return implode(',', array_keys($this->getCategorias()));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tipo::query();

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('status')) {
            $query->where('ativo', $request->status === 'ativo');
        }

        if ($request->filled('busca')) {
            $query->where('nome', 'like', '%' . $request->busca . '%');
        }

        $tipos = $query->orderBy('categoria')->orderBy('nome')->paginate(15);
        $categorias = $this->getCategorias();

        return view('tipos.index', compact('tipos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = $this->getCategorias();
        return view('tipos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:tipos,nome',
            'descricao' => 'nullable|string|max:500',
            'categoria' => 'required|string|in:' . $this->getCategoriasKeys(),
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique' => 'Já existe um tipo com este nome.',
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in' => 'Categoria inválida.',
        ]);

        Tipo::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'ativo' => true,
        ]);

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo de peça cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tipo = Tipo::findOrFail($id);
        $categorias = $this->getCategorias();

        return view('tipos.edit', compact('tipo', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tipo = Tipo::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255|unique:tipos,nome,' . $id,
            'descricao' => 'nullable|string|max:500',
            'categoria' => 'required|string|in:' . $this->getCategoriasKeys(),
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique' => 'Já existe um tipo com este nome.',
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in' => 'Categoria inválida.',
        ]);

        $tipo->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
        ]);

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo de peça atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tipo = Tipo::findOrFail($id);

        if ($tipo->coletaPecas()->count() > 0) {
            return redirect()->route('tipos.index')
                ->with('error', 'Não é possível excluir este tipo pois há peças associadas.');
        }

        $tipo->delete();

        return redirect()->route('tipos.index')
            ->with('success', 'Tipo de peça excluído com sucesso!');
    }

    /**
     * Toggle status ativo/inativo
     */
    public function toggleStatus($id)
    {
        $tipo = Tipo::findOrFail($id);
        $tipo->ativo = !$tipo->ativo;
        $tipo->save();

        $status = $tipo->ativo ? 'ativado' : 'desativado';

        return redirect()->route('tipos.index')
            ->with('success', "Tipo de peça {$status} com sucesso!");
    }
}
