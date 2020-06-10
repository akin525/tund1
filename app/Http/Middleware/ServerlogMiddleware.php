<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\UltilityController;
use App\Model\Serverlog;
use Closure;

class ServerlogMiddleware extends UltilityController
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

        $this->monnifyRA($request->user_name);

        return $next($request);
    }
}
