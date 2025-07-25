<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has role 'Member'
        if (LoginUser()->role == 'SubAdmin' || LoginUser()->role == 'Admin') {
            return redirect()->route('admin.accounts');
            abort(403, 'Unauthorized');
            }

            return $next($request);
    }
}
