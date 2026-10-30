<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $stats = [
            'active'    => Subscription::active()->count(),
            'mrr'       => (float) Subscription::active()->sum('price'),
            'collected' => (float) Subscription::sum('price'),
        ];

        $byPlan = Subscription::active()
            ->selectRaw('plan_name, count(*) as total, sum(price) as revenue')
            ->groupBy('plan_name')
            ->get();

        $subscriptions = Subscription::with('user')
            ->latest('starts_at')
            ->paginate(15);

        return view('admin.subscriptions.index', compact('stats', 'byPlan', 'subscriptions'));
    }
}