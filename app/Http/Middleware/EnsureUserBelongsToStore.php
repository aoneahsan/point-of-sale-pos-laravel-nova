<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $storeId = $request->route('store') ?? $request->input('store_id');

        if ($user->store_id && $user->store_id != $storeId) {
            abort(403, 'Unauthorized access to this store.');
        }

        return $next($request);
    }
}