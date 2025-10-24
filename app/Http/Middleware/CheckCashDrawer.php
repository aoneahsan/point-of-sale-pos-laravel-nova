<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CashDrawer;

class CheckCashDrawer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('pos.require_cash_drawer', true)) {
            return $next($request);
        }

        $user = $request->user();
        
        $openDrawer = CashDrawer::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (!$openDrawer) {
            return redirect()->route('cash-drawer.open')
                ->with('error', 'Please open a cash drawer before processing sales.');
        }

        return $next($request);
    }
}