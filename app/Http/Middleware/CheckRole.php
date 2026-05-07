<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur connecté possède l'un des rôles autorisés.
     * Usage dans les routes : ->middleware('role:admin')
     *                     ou  ->middleware('role:admin,cp')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, 'Accès non autorisé.');
        }

        if (!in_array($user->role->name, $roles)) {
            // Rediriger vers le bon dashboard plutôt qu'une page 403 brute
            $role = $user->role->name;
            $route = match ($role) {
                'admin' => 'dashboard.admin',
                'cp'    => 'dashboard.cp',
                'sup'   => 'dashboard.sup',
                'tc'    => 'dashboard.tc',
                default => 'dashboard',
            };
            return redirect()->route($route)->with('error', 'Vous n\'avez pas accès à cette section.');
        }

        return $next($request);
    }
}
