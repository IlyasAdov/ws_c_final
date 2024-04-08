<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidWorkspace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $workspace = Workspace::findOrFail($request->workspaceId);

        if (!$workspace) {
            abort(404);
        }

        // Проверка авторизации пользователя и его доступа к рабочему пространству.
        if (!$request->user() || $workspace->user_id !== $request->user()->id) {
            abort(403);
        }

        return $next($request);
    }
}
