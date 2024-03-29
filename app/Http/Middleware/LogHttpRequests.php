<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\HttpRequest;
use Auth;

class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            $requestData = [
                'user_id'   => Auth::id(),
                'url'       => $request->fullUrl(),
                'method'    => $request->method(),
                'ip'        => $request->ip(),
                'body'      => json_encode($request->all()),
                'referer'   => $request->headers->get('referer'),
            ];
    
            HttpRequest::create($requestData);
        }

        return $next($request);
    }
}