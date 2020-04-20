<?php


namespace App\Http\Middleware;

use Closure;

class AuthMiddleware {
    public function handle($request, Closure $next)
    {
        return auth()->guest() || !auth()->check() ? redirect('/discord_login') : $next($request);
    }
}
