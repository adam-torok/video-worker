<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DomainCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedHosts = explode(',', env('ALLOWED_DOMAINS'));
        $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
            if(!\in_array($requestHost, $allowedHosts, false)) {
                $requestInfo = [
                    'host' => $requestHost,
                    'ip' => $request->getClientIp(),
                    'url' => $request->getRequestUri(),
                    'agent' => $request->header('User-Agent'),
                ];
                throw new SuspiciousOperationException("Not allowed domain!");
            }
        return $next($request);
    }
}
