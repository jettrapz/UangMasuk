<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LedgerRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->session()->get('ledger_role') !== $role) {
            return redirect()->route('login', $role);
        }

        return $next($request);
    }
}