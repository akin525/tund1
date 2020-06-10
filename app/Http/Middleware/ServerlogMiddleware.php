<?php

namespace App\Http\Middleware;

use App\Model\Serverlog;
use Closure;

class ServerlogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Serverlog::create($request->all());
        return $next($request);
    }
}
