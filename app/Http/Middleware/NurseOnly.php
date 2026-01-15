<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NurseOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userRole = session('user_role');
        
        if ($userRole !== 'nurse') {
            // Return JSON for AJAX requests, redirect for regular requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only nurses can perform this action.'
                ], 403);
            }
            return back()->with('error', 'Only nurses can perform this action.');
        }
        
        return $next($request);
    }
}
