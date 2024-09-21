<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestsAndResponses
{

    public function handle(Request $request, Closure $next)
    {
        Log::info('incomming request', ['request' => $request->all()]);

        $response = $next($request);

        Log::info(' outgoing response', ['response' => $response]);

        return $response;

    }
}