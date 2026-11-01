<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Mga customer na nag-book sa salon ng may-ari
    public function index(Request $request)
    {
        $salonId = $request->user()->salon->id;

        $customers = User::whereHas('appointments', fn ($q) => $q->where('salon_id', $salonId))
            ->withCount(['appointments as visits' => fn ($q) => $q->where('salon_id', $salonId)])
            ->withMax(['appointments as last_visit' => fn ($q) => $q->where('salon_id', $salonId)], 'appointment_at')
            ->orderBy('name')
            ->paginate(15);

        return view('owner.customers.index', compact('customers'));
    }
}