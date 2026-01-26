<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleApiErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If there's a validation error, format it nicely
        if ($response->exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $response->exception->errors(),
            ], 422);
        }

        return $response;
    }
}
