<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiToken;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAPITokenAndQuota
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-API-TOKEN');
        $isValidToken = ApiToken::where('token', $token)->first();

        if (!$token || !$isValidToken || $isValidToken->revoked_at != null) {
            return response()->json(['error' => 'Invalid/Revoked API token'], 401);
        }

        $workspace = Workspace::findOrFail($isValidToken->workspace_id);

        $costsCurrentMonth = $workspace->getBill($workspace->id)['total'];

        if ($workspace->billingQuota && ($workspace->billingQuota->limit - $costsCurrentMonth) < 0) {
            return response()->json(['error' => 'Quota Exceeded'], 403);
        }

        return $next($request);
    }
}
