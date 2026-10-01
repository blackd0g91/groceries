<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePassword
{
    public const SESSION_KEY = 'password_confirmed';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get(self::SESSION_KEY)) {
            if ($request->expectsJson()) {
                abort(401);
            }

            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
