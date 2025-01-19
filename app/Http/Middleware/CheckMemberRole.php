<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckMemberRole
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
        // Comprehensive logging
        Log::info('Member Role Middleware Check', [
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::check() ? Auth::id() : 'N/A',
            'user_email' => Auth::check() ? Auth::user()->email : 'N/A',
            'user_role' => Auth::check() ? Auth::user()->role : 'not_authenticated',
            'is_member' => Auth::check() ? Auth::user()->isMember() : false
        ]);

        // Explicit authentication and role check
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt', [
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            return redirect()->route('login')
                ->withErrors(['error' => 'Please log in to access this feature.']);
        }

        if (!Auth::user()->isMember()) {
            Log::warning('Non-member access attempt', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'user_role' => Auth::user()->role,
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            return redirect()->route('reminders.index')
                ->withErrors(['error' => 'You do not have permission to perform this action.']);
        }

        return $next($request);
    }
}
