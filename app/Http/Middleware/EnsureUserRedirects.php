<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRedirects
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $usuario = auth()->user();
        $nivel = $usuario->nivelAcesso->nome ?? null;

        // Administrador e Motorista seguem fluxo padrão
        if (in_array($nivel, ['Administrador', 'Motorista'])) {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        $targets = [
            'Gestor' => 'painel',
            'Pesagem' => 'pesagem.index',
            'Empacotamento' => 'empacotamento.index',
        ];

        if (array_key_exists($nivel, $targets)) {
            $targetRoute = $targets[$nivel];

            $allowedRoutes = [
                'Gestor' => ['painel', 'acompanhar-coletas', 'coletas.*', 'empacotamento.*', 'pesagem.*', 'estabelecimentos.*'],
                'Pesagem' => ['pesagem.*', 'coletas.*'],
                'Empacotamento' => ['empacotamento.*', 'coletas.*'],
            ];

            $isAllowed = false;
            foreach ($allowedRoutes[$nivel] as $allowed) {
                if (str_ends_with($allowed, '.*')) {
                    $prefix = rtrim($allowed, '.*');
                    if (str_starts_with($routeName, $prefix)) {
                        $isAllowed = true;
                        break;
                    }
                } elseif ($routeName === $allowed) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return redirect()->route($targetRoute);
            }
        }

        return $next($request);
    }
}


