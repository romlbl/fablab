<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->session()->get('auth_role');

        if (! $role) {
            return redirect()->route('connexion');
        }

        if ($role === 'admin') {
            return redirect()->route('admin.accueil');
        }

        if ($role !== 'client' || ! $request->session()->get('auth_user_id')) {
            return redirect()->route('connexion');
        }

        return $next($request);
    }
}