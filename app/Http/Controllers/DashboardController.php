<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Salon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Platform admin
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
                    'salons'        => Salon::active()->count(),
                ],
                'upcoming' => Appointment::with(['user', 'staff'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('appointment_at', '>=', now())
                    ->orderBy('appointment_at')
                    ->limit(5)
                    ->get(),
            ]);
        }

        // Salon owner
        if ($user->isSalonOwner()) {
            $salon        = $user->salon;
            $subscription = $user->activeSubscription();
            $stats        = null;
            $upcoming     = collect();

            if ($salon && $subscription) {
                $stats = [
                    'today'     => $salon->appointments()
                        ->whereDate('appointment_at', today())
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count(),
                    'pending'   => $salon->appointments()->where('status', 'pending')->count(),
                    'staff'     => $salon->staff()->count(),
                    'customers' => $salon->appointments()->distinct()->count('user_id'),
                    // Pansamantala: kita mula sa completed na appointments (papalitan ng Transactions sa Part 4)
                    'revenue'   => (float) $salon->appointments()
                        ->where('status', 'completed')
                        ->whereMonth('appointment_at', now()->month)
                        ->whereYear('appointment_at', now()->year)
                        ->sum('service_price'),
                ];

                $upcoming = $salon->appointments()
                    ->with(['user', 'staff'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('appointment_at', '>=', now())
                    ->orderBy('appointment_at')
                    ->limit(5)
                    ->get();
            }

            return view('dashboard-owner', compact('salon', 'subscription', 'stats', 'upcoming'));
        }

        // Customer
        return view('dashboard', [
            'isAdmin' => false,
            'next' => $user->appointments()->with('staff')
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('appointment_at', '>=', now())
                ->orderBy('appointment_at')
                ->first(),
            'recent' => $user->appointments()->latest('appointment_at')->limit(5)->get(),
        ]);
    }
}