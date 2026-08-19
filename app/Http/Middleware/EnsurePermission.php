<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()?->can($permission)) {
            // AuthorizationException defaults to an English, unlocalized
            // message when thrown bare — pass api.forbidden explicitly so
            // the bootstrap/app.php handler doesn't have anything to fall
            // back on.
            throw new AuthorizationException(__('api.forbidden'));
        }

        return $next($request);
    }
}
