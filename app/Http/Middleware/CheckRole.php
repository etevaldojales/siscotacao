<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Role;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            // Not authenticated
            return redirect('/login');
        }

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            // Unauthorized role
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
