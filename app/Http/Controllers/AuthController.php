<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\NivelAcesso;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            // Redireciona motoristas para o dashboard específico
            if ($usuario->nivelAcesso && $usuario->nivelAcesso->nome === 'Motorista') {
                return redirect()->route('motorista.dashboard');
            }
            return redirect()->route('painel');
        }

        return view('auth.login');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
            'password' => 'required'
        ], [
            'cpf.required' => 'O campo CPF é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.'
        ]);

        // Remove formatação do CPF (pontos e hífen)
        $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);
        $cpfFormatado = $request->cpf;
        
        // Verifica se o usuário existe e está ativo (tenta primeiro com formatação, depois sem)
        $usuario = Usuario::where('cpf', $cpfFormatado)
                         ->where('ativo', true)
                         ->first();
                         
        if (!$usuario) {
            $usuario = Usuario::where('cpf', $cpfLimpo)
                             ->where('ativo', true)
                             ->first();
        }

        if (!$usuario) {
            return back()->withErrors([
                'cpf' => 'Usuário não encontrado ou inativo.'
            ])->withInput();
        }

        // Verifica a senha manualmente
        if (Hash::check($request->password, $usuario->password)) {
            Auth::login($usuario, $request->filled('remember'));
            $request->session()->regenerate();

            // Atualiza último login
            $usuario->ultimo_login = now();
            $usuario->save();

            // Redireciona motoristas para o dashboard específico
            if ($usuario->nivelAcesso && $usuario->nivelAcesso->nome === 'Motorista') {
                return redirect()->route('motorista.dashboard');
            }

            return redirect()->intended(route('painel'));
        }

        return back()->withErrors([
            'cpf' => 'As credenciais fornecidas não conferem com nossos registros.'
        ])->withInput();
    }

    /**
     * Exibe o formulário de cadastro
     */
    public function showCadastro()
    {
        $niveisAcesso = NivelAcesso::where('ativo', true)->get();
        return view('auth.cadastro', compact('niveisAcesso'));
    }

    /**
     * Processa o cadastro
     */
    public function cadastro(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:6|confirmed',
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:usuarios',
            'nivel_acesso_id' => 'required|exists:niveis_acesso,id'
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'cpf.unique' => 'Este CPF já está em uso.',
            'nivel_acesso_id.required' => 'Selecione um nível de acesso.',
            'nivel_acesso_id.exists' => 'Nível de acesso inválido.'
        ]);

        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefone' => $request->telefone,
            'cpf' => $request->cpf,
            'nivel_acesso_id' => $request->nivel_acesso_id,
            'ativo' => true
        ]);

        Auth::login($usuario);

        return redirect()->route('painel')->with('success', 'Cadastro realizado com sucesso!');
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Processa logout via GET (para casos de acesso direto)
     */
    public function logoutGet(Request $request)
    {
        // Se não há sessão ativa, redirecionar para página inicial
        if (!auth()->check()) {
            return redirect()->route('home')->with('info', 'Você não estava logado.');
        }

        // Se há sessão ativa, fazer logout sem verificação CSRF para GET
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }
}
