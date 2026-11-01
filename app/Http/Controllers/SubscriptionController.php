<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Notifications\SubscriptionActivated;
use App\Services\StrapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    // My Subscription
    public function index(Request $request)
    {
        return view('subscriptions.index', [
            'subscriptions' => $request->user()->subscriptions()->latest('starts_at')->get(),
            'active'        => $request->user()->activeSubscription(),
        ]);
    }

    // Checkout page
    public function create(Request $request, StrapiService $strapi, string $plan)
    {
        if ($request->user()->activeSubscription()) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'You already have an active subscription. Cancel it first to switch plans.');
        }

        $selected = collect($strapi->plans() ?? [])->firstWhere('documentId', $plan);

        abort_unless($selected, 404, 'Plan not found, or the plan list is unavailable.');

        return view('subscriptions.create', ['plan' => $selected]);
    }

    // I-save ang subscription (demo payment) at likhain ang salon
    public function store(Request $request, StrapiService $strapi, string $plan)
    {
        $user = $request->user();

        if ($user->activeSubscription()) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'You already have an active subscription.');
        }

        $data = $request->validate([
            'salon_name' => 'required|string|max:255',
        ]);

        // Ang presyo ay kinukuha sa CMS, hindi sa ipinadala ng browser
        $selected = collect($strapi->plans() ?? [])->firstWhere('documentId', $plan);

        abort_unless($selected, 404, 'Plan not found, or the plan list is unavailable.');

        // Isang salon lang ang bawat may-ari. Gagawa kung wala pa, at ia-update ang pangalan kung meron na.
        $salon = $user->salon()->updateOrCreate([], ['name' => $data['salon_name']]);

        $subscription = $salon->subscriptions()->create([
            'user_id'                  => $user->id,
            'plan_document_id'         => $selected['documentId'],
            'plan_name'                => $selected['name'],
            'price'                    => $selected['price'],
            'max_staff'                => $selected['max_staff'] ?? null,
            'max_monthly_appointments' => $selected['max_monthly_appointments'] ?? null,
            'salon_name'               => $salon->name,
            'status'                   => 'active',
            'reference_no'             => 'SS-' . strtoupper(Str::random(8)),
            'starts_at'                => now(),
            'ends_at'                  => now()->addMonth(),
        ]);

        try {
            $user->notify(new SubscriptionActivated($subscription));
        } catch (\Throwable $e) {
            Log::warning('Failed to send subscription email: ' . $e->getMessage());
        }

        return redirect()->route('subscriptions.index')
            ->with('success', 'Your subscription is now active!');
    }

    // I-cancel ang sariling subscription
    public function cancel(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);

        if ($subscription->status === 'active') {
            $subscription->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        return back()->with('success', 'Subscription cancelled.');
    }
}