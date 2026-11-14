<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $salons = Salon::with([
                'owner',
                'subscriptions' => fn ($query) => $query->active(),
            ])
            ->withCount(['staff', 'appointments'])
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('address', 'like', "%{$q}%")
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.salons.index', compact('salons', 'q'));
    }

    // Itatago sa publiko ang suspendidong salon at hindi na ito makakapag-book
    public function suspend(Salon $salon)
    {
        $salon->forceFill(['suspended_at' => now()])->save();

        return back()->with('success', "{$salon->name} has been suspended. It is hidden from customers and cannot take bookings.");
    }

    public function restore(Salon $salon)
    {
        $salon->forceFill(['suspended_at' => null])->save();

        return back()->with('success', "{$salon->name} is active again.");
    }
}
