<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class ApiSessionMiddleware
{
    protected $except = [
        'api/user/login',
        'api/user/register',
        'api/user/logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->path(), $this->except)) {

            if (!auth('sanctum')->check()) {
                return response()->json([
                    'success' => false,
                    'data' => 'NOT AUTHENTICATED',
                    'message' => "Please Login",
                ], 401);
            }
        }
        return $next($request);
    }
}
