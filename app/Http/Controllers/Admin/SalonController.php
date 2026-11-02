<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salon;

class SalonController extends Controller
{
    public function index()
    {
        $salons = Salon::with([
                'owner',
                'subscriptions' => fn ($q) => $q->active(),
            ])
            ->withCount(['staff', 'appointments'])
            ->latest()
            ->paginate(15);

        return view('admin.salons.index', compact('salons'));
    }
}