<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $salon = $request->user()->salon;

        return view('owner.staff.index', [
            'staff' => $salon->staff()->orderBy('name')->get(),
            // Limit ayon sa plan; null ang ibig sabihin ay unlimited
            'limit' => $request->user()->activeSubscription()?->max_staff,
        ]);
    }

    public function create()
    {
        return view('owner.staff.create');
    }

    public function store(Request $request)
    {
        $user  = $request->user();
        $salon = $user->salon;

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'role'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $limit = $user->activeSubscription()?->max_staff;

        if ($limit !== null && $salon->staff()->count() >= $limit) {
            return back()->withInput()->withErrors([
                'name' => "Your plan allows up to {$limit} staff members. Upgrade your plan to add more.",
            ]);
        }

        $salon->staff()->create($data);

        return redirect()->route('owner.staff.index')->with('success', 'Staff member added.');
    }

    public function destroy(Request $request, Staff $staff)
    {
        // Sariling salon lang ang puwedeng galawin
        abort_unless($staff->salon_id === $request->user()->salon->id, 403);

        $staff->delete();

        return back()->with('success', 'Staff member removed.');
    }
}