<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SalonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = $request->user()->salon->services()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('owner.services.index', compact('services'));
    }

    public function create()
    {
        return view('owner.services.form', [
            'service'    => null,
            'categories' => SalonService::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        $request->user()->salon->services()->create($data);

        return redirect()->route('owner.services.index')->with('success', 'Service added.');
    }

    public function edit(Request $request, SalonService $service)
    {
        $this->authorizeService($request, $service);

        return view('owner.services.form', [
            'service'    => $service,
            'categories' => SalonService::CATEGORIES,
        ]);
    }

    public function update(Request $request, SalonService $service)
    {
        $this->authorizeService($request, $service);

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($service);
            $data['image_path'] = $request->file('image')->store('services', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($service);
            $data['image_path'] = null;
        }

        $service->update($data);

        return redirect()->route('owner.services.index')->with('success', 'Service updated.');
    }

    public function destroy(Request $request, SalonService $service)
    {
        $this->authorizeService($request, $service);

        $this->deleteImage($service);
        $service->delete();

        return back()->with('success', 'Service removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'category'         => ['required', Rule::in(array_keys(SalonService::CATEGORIES))],
            'description'      => 'nullable|string|max:500',
            'price'            => 'required|numeric|min:1|max:99999',
            'duration_minutes' => 'required|integer|min:5|max:600',
            'image'            => 'nullable|image|max:3072',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    // Sariling salon lang ang puwedeng galawin
    private function authorizeService(Request $request, SalonService $service): void
    {
        abort_unless($service->salon_id === $request->user()->salon->id, 403);
    }

    private function deleteImage(SalonService $service): void
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
    }
}
