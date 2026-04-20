<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\NivelAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Usuario::with('nivelAcesso');

        // Filtro por busca (nome, email, CPF)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        // Filtro por nível de acesso
        if ($request->filled('nivel_acesso')) {
            $query->where('nivel_acesso_id', $request->nivel_acesso);
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('ativo', $request->status);
        }

        $usuarios = $query->orderBy('nome')->paginate(10);
        
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $niveisAcesso = NivelAcesso::ativos()->orderBy('nome')->get();
        return view('usuarios.create', compact('niveisAcesso'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:usuarios,cpf',
            'nivel_acesso_id' => 'required|exists:niveis_acesso,id',
            'ativo' => 'boolean',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'nivel_acesso_id.required' => 'O nível de acesso é obrigatório.',
            'nivel_acesso_id.exists' => 'Nível de acesso inválido.',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['ativo'] = $request->has('ativo');

        // Limpar CPF se fornecido
        if ($data['cpf']) {
            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
        }

        Usuario::create($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = Usuario::with('nivelAcesso')->findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $niveisAcesso = NivelAcesso::ativos()->orderBy('nome')->get();
        return view('usuarios.edit', compact('usuario', 'niveisAcesso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('usuarios')->ignore($id)],
            'password' => 'nullable|string|min:6|confirmed',
            'telefone' => 'nullable|string|max:20',
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('usuarios')->ignore($id)],
            'nivel_acesso_id' => 'required|exists:niveis_acesso,id',
            'ativo' => 'boolean',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'nivel_acesso_id.required' => 'O nível de acesso é obrigatório.',
            'nivel_acesso_id.exists' => 'Nível de acesso inválido.',
        ]);

        $data = $request->all();
        
        // Só atualizar senha se foi fornecida
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        $data['ativo'] = $request->has('ativo');

        // Limpar CPF se fornecido
        if ($data['cpf']) {
            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        // Não permitir excluir o próprio usuário
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Você não pode excluir seu próprio usuário.');
        }
        
        // Verificar se há registros associados
        if ($usuario->coletas()->count() > 0 || $usuario->empacotamentos()->count() > 0) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Não é possível excluir este usuário pois há registros associados.');
        }
        
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Toggle status ativo/inativo
     */
    public function toggleStatus($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        // Não permitir desativar o próprio usuário
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Você não pode desativar seu próprio usuário.');
        }
        
        $usuario->ativo = !$usuario->ativo;
        $usuario->save();

        $status = $usuario->ativo ? 'ativado' : 'desativado';
        
        return redirect()->route('usuarios.index')
            ->with('success', "Usuário {$status} com sucesso!");
    }
}