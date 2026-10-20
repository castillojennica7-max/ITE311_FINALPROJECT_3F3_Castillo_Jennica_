<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        return view('staff.index', [
            'staffMembers' => Staff::latest()->get(),
        ]);
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'role'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        Staff::create($data);

        return redirect()->route('staff.index')
                         ->with('success', 'Staff member added!');
    }
}