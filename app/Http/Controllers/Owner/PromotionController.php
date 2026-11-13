<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SalonPromotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $promotions = $request->user()->salon->promotions()->latest()->get();

        return view('owner.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('owner.promotions.form', ['promotion' => null]);
    }

    public function store(Request $request)
    {
        $request->user()->salon->promotions()->create($this->validated($request));

        return redirect()->route('owner.promotions.index')->with('success', 'Promotion added.');
    }

    public function edit(Request $request, SalonPromotion $promotion)
    {
        $this->authorizePromotion($request, $promotion);

        return view('owner.promotions.form', compact('promotion'));
    }

    public function update(Request $request, SalonPromotion $promotion)
    {
        $this->authorizePromotion($request, $promotion);

        $promotion->update($this->validated($request));

        return redirect()->route('owner.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(Request $request, SalonPromotion $promotion)
    {
        $this->authorizePromotion($request, $promotion);

        $promotion->delete();

        return back()->with('success', 'Promotion removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type'              => ['required', Rule::in(['promo', 'bundle'])],
            'title'             => 'required|string|max:120',
            'description'       => 'nullable|string|max:500',
            'discount_percent'  => 'nullable|integer|between:1,90',
            'bundle_price'      => 'nullable|numeric|min:1|max:99999',
            'included_services' => 'nullable|string|max:600',
            'valid_until'       => 'nullable|date',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    // Sariling salon lang ang puwedeng galawin
    private function authorizePromotion(Request $request, SalonPromotion $promotion): void
    {
        abort_unless($promotion->salon_id === $request->user()->salon->id, 403);
    }
}
