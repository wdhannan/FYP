<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('authenticated')) {
            return redirect()->route('login')->withErrors([
                'id' => 'Please login to access this page.',
            ]);
        }

        // Check if user must change password (except on password change page itself)
        if (!$request->routeIs('password.change') && !$request->routeIs('password.change.post')) {
            $userId = session('user_id');
            if ($userId) {
                $user = \Illuminate\Support\Facades\DB::table('user')->where('UserID', $userId)->first();
                if ($user && $user->must_change_password) {
                    return redirect()->route('password.change');
                }
            }
        }
        
        return $next($request);
    }
}
