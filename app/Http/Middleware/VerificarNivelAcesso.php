<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarNivelAcesso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissoes): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $usuario = auth()->user();

        // Verifica se o usuário está ativo
        if (!$usuario->ativo) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Sua conta foi desativada.']);
        }

        // Se não foram especificadas permissões, apenas verifica se está logado
        if (empty($permissoes)) {
            return $next($request);
        }

        // Verifica se o usuário tem pelo menos uma das permissões necessárias
        foreach ($permissoes as $permissao) {
            if ($usuario->temPermissao($permissao)) {
                return $next($request);
            }
        }

        // Se chegou até aqui, não tem permissão
        abort(403, 'Você não tem permissão para acessar esta página.');
    }
}
