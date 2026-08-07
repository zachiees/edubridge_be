<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {   $current_user = $request->user();
        $current_role = $current_user->role->name ?? null;
        if(!in_array($current_role,$roles)){
            abort(Response::HTTP_FORBIDDEN , 'RoleGuard:Invalid Role Permission');
        }
        return $next($request);
    }
}
