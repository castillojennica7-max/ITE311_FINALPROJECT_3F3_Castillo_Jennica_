<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    public function edit(Request $request)
    {
        return view('owner.settings.edit', ['salon' => $request->user()->salon]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $request->user()->salon->update($data);

        return back()->with('success', 'Salon details updated.');
    }
}