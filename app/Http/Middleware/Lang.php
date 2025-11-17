<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Lang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(in_array($request->header('locale'), ['ar', 'en', 'de', 'fil', 'tl']) ? $request->header('locale') : 'ar');
        return $next($request);
    }
}
