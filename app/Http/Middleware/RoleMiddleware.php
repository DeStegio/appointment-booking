<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Usage: ->middleware(['auth','role:provider']) or multiple: role:admin|provider
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            // let the auth middleware handle guests; as a fallback:
            return redirect()->route('login');
        }

        // support role lists separated by "|" or ","
        $list = collect($roles)
            ->flatMap(fn ($r) => preg_split('/[|,]/', (string) $r))
            ->map(fn ($r) => trim($r))
            ->filter()
            ->values()
            ->all();

        // allow wildcard "*"
        if (in_array('*', $list, true)) {
            return $next($request);
        }

        // compare case-insensitively against user's role
        foreach ($list as $role) {
            if (strcasecmp((string)$user->role, $role) === 0) {
                return $next($request);
            }
        }

        abort(403, 'Forbidden: insufficient role.');
    }
}

