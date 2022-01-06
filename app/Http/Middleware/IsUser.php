<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class IsUser
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
        $user = User::where('token_user', $request->bearerToken())->first();
        if (isset($user->active)) {
            if ($user->active) {
                $role = Role::where('id', $user->role_id)->first();

                $check = $role->level;
            }
        }

        if ($check != 4) {
            return response()->json([
                'message' => 'Bạn chưa đăng nhập',
                'success' => false
            ]);
        }

        return $next($request);
    }
}
