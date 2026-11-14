<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    // Kapag sinuspinde ng admin ang account, awtomatikong ilo-logout ito sa susunod na request
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->suspended_at) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Please contact the SalonSync administrator.',
            ]);
        }

        return $next($request);
    }
}
