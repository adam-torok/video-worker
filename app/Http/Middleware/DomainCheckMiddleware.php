<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DomainCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $allowedHosts = explode(',', env('ALLOWED_DOMAINS'));
            $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
            if(!\in_array($requestHost, $allowedHosts, false)) {
                $requestInfo = [
                    'host' => $requestHost,
                    'ip' => $request->getClientIp(),
                    'url' => $request->getRequestUri(),
                    'agent' => $request->header('User-Agent'),
                ];
            }
            return $next($request);
        } catch (\Throwable $th) {
            echo "Not allowed domain!";
        }  
    }
}
