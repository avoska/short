<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class FixPage1
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->input('page')) {
            return redirect($request->getPathInfo(), 301);
        }

        /** @var Response $response */
        $response = $next($request);
        $response->setContent(str_replace('?page=1"', '"', $response->getContent()));

        return $response;
    }
}
