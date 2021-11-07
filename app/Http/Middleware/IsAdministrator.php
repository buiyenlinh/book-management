<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $check = 0;
        $user = User::where('token', $request->bearerToken())->first();
        if ($user->active) {
            $check = Role::where('id', $user->role_id)->first()->level;
        }
        
        if ($check != 2) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access'
            ]);
        }
        return $next($request);
    }
}
