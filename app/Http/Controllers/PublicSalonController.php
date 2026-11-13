<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\Request;

class PublicSalonController extends Controller
{
    // Listahan ng lahat ng salon na tumatanggap ng booking
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $salons = Salon::active()
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('address', 'like', "%{$q}%")
            ))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('salons.index', compact('salons', 'q'));
    }

    // Pahina ng isang salon: larawan, oras, serbisyo at presyo, promo, at staff
    public function show(Salon $salon)
    {
        abort_unless($salon->isActive(), 404);

        $services = $salon->services()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('salons.show', [
            'salon'      => $salon,
            'services'   => $services,
            'promotions' => $salon->promotions()->current()->latest()->get(),
            'staff'      => $salon->staff()->orderBy('name')->get(),
        ]);
    }
}
