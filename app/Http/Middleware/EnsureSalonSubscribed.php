<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSalonSubscribed
{
    // Kailangang may aktibong subscription ang salon ng may-ari, at hindi ito suspendido
    public function handle(Request $request, Closure $next): Response
    {
        $salon = $request->user()?->salon;

        if ($salon && $salon->suspended_at) {
            return redirect()->route('dashboard')
                ->with('error', 'Your salon has been suspended by the SalonSync administrator. Please contact support.');
        }

        if (! $salon || ! $salon->isActive()) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Please subscribe to a plan to use the salon management tools.');
        }

        return $next($request);
    }
}
