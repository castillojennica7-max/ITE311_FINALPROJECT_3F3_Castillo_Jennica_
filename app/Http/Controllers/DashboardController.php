<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard', [
                'isAdmin' => true,
                'stats' => [
                    'customers'     => User::where('role', 'customer')->count(),
                    'appointments'  => Appointment::count(),
                    'pending'       => Appointment::where('status', 'pending')->count(),
                    'revenue'       => (float) Appointment::where('status', 'completed')->sum('service_price'),
                    'subscriptions' => Subscription::active()->count(),
                    'mrr'           => (float) Subscription::active()->sum('price'),
                ],
                'upcoming' => Appointment::with(['user', 'staff'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('appointment_at', '>=', now())
                    ->orderBy('appointment_at')
                    ->limit(5)
                    ->get(),
            ]);
        }

        return view('dashboard', [
            'isAdmin' => false,
            'next' => $user->appointments()->with('staff')
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('appointment_at', '>=', now())
                ->orderBy('appointment_at')
                ->first(),
            'recent'       => $user->appointments()->latest('appointment_at')->limit(5)->get(),
            'subscription' => $user->activeSubscription(),
        ]);
    }
}