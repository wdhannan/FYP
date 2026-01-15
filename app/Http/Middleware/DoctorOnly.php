<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userRole = session('user_role');
        
        if ($userRole !== 'doctor') {
            // Return JSON for AJAX requests, redirect for regular requests
            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only doctors can perform this action.'
                ], 403);
            }
            return back()->with('error', 'Access denied. Only doctors can perform this action.');
        }
        
        return $next($request);
    }
}
