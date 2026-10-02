<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $allowedRoles = collect($roles)
            ->flatMap(fn ($role) => preg_split('/[|,]/', (string) $role))
            ->filter()
            ->values()
            ->all();

        $user = $request->user();

        abort_unless(
            $user && $user->hasAnyRole($allowedRoles),
            403,
            'You are not authorized to access this page.'
        );

        return $next($request);
    }
}