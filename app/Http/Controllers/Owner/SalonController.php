<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SalonController extends Controller
{
    public function edit(Request $request)
    {
        $salon = $request->user()->salon;

        return view('owner.settings.edit', [
            'salon' => $salon,
            'hours' => $salon->hours(),
            'days'  => Salon::DAYS,
        ]);
    }

    public function update(Request $request)
    {
        $salon = $request->user()->salon;

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'tagline' => 'nullable|string|max:120',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'about'   => 'nullable|string|max:2000',
            'logo'    => 'nullable|image|max:2048',
            'cover'   => 'nullable|image|max:4096',
            'hours'   => 'required|array',
        ]);

        // Oras ng pagbubukas: sinusuri bawat araw
        $hours  = [];
        $errors = [];

        foreach (Salon::DAYS as $key => $label) {
            $closed = $request->boolean("hours.$key.closed");
            $open   = $request->input("hours.$key.open") ?: '09:00';
            $close  = $request->input("hours.$key.close") ?: '18:00';

            $valid = preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $open) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $close);

            if (! $valid) {
                $errors["hours.$key.open"] = "Please enter a valid opening and closing time for {$label}.";
                continue;
            }

            if (! $closed && $close <= $open) {
                $errors["hours.$key.close"] = "Closing time on {$label} must be later than the opening time.";
            }

            $hours[$key] = ['open' => $open, 'close' => $close, 'closed' => $closed];
        }

        if ($errors) {
            return back()->withInput()->withErrors($errors);
        }

        // Mga larawan
        foreach (['logo' => 'logo_path', 'cover' => 'cover_path'] as $input => $column) {
            if ($request->hasFile($input)) {
                $this->deleteStored($salon->$column);
                $data[$column] = $request->file($input)->store("salons/{$input}s", 'public');
            } elseif ($request->boolean("remove_$input")) {
                $this->deleteStored($salon->$column);
                $data[$column] = null;
            }
        }

        unset($data['logo'], $data['cover'], $data['hours']);

        $salon->update($data + ['opening_hours' => $hours]);

        return back()->with('success', 'Salon details updated.');
    }

    // Binubura lang ang mga file na in-upload; hindi ang mga default na larawan sa public/images
    private function deleteStored(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
